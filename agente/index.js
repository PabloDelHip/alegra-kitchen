const express = require('express');
const axios = require('axios');
require('dotenv').config();

const app = express();
app.use(express.json());

const API_KEY = process.env.GROQ_API_KEY;
const GROQ_URL = process.env.GROQ_URL || 'https://api.groq.com/openai/v1/chat/completions';

const KITCHEN_SERVICE_URL = process.env.KITCHEN_SERVICE_URL;
const WAREHOUSE_PURCHASES_URL = process.env.WAREHOUSE_PURCHASES_URL;
const WAREHOUSE_INGREDIENTS_URL = process.env.WAREHOUSE_INGREDIENTS_URL;

const getDisplayName = (name) => {
  const names = {
    tomato: 'Tomate',
    lemon: 'Limón',
    potato: 'Papa',
    rice: 'Arroz',
    ketchup: 'Catsup',
    lettuce: 'Lechuga',
    onion: 'Cebolla',
    cheese: 'Queso',
    meat: 'Carne',
    chicken: 'Pollo',
  };
  return names[name] || name.charAt(0).toUpperCase() + name.slice(1);
};

app.get('/api/recommendations', async (req, res) => {
  const today = new Date().toISOString().split('T')[0];

  const kitchenOrdersUrl = `${KITCHEN_SERVICE_URL}?date=${today}&per_page=100`;
  const purchasesUrl = `${WAREHOUSE_PURCHASES_URL}?date=${today}`;
  const ingredientsUrl = WAREHOUSE_INGREDIENTS_URL;

  try {
    const [ordersRes, purchasesRes, ingredientsRes] = await Promise.all([
      axios.get(kitchenOrdersUrl),
      axios.get(purchasesUrl),
      axios.get(ingredientsUrl),
    ]);

    const ordersData = ordersRes.data;
    const stockData = purchasesRes.data;
    const ingredientsData = ingredientsRes.data;

    if (!ordersData?.data || !stockData?.data || !ingredientsData?.data) {
      return res.status(400).json({ error: 'Alguno de los endpoints no devolvió datos válidos' });
    }

    // Resumen de platillos
    const resumenPlatillos = {};
    ordersData.data.forEach(order => {
      order.dishes.forEach(dish => {
        const nombre = dish.recipe.name;
        if (!resumenPlatillos[nombre]) resumenPlatillos[nombre] = 0;
        resumenPlatillos[nombre]++;
      });
    });

    const listadoPlatillos = Object.entries(resumenPlatillos)
      .map(([nombre, cantidad]) => `- ${nombre}: ${cantidad} pedidos`)
      .join('\n');

    const listadoStock = stockData.data
      .map(item => `- ${getDisplayName(item.name)}: ${item.total_quantity} unidades compradas hoy (última actualización: ${item.last_update ?? 'sin registro'})`)
      .join('\n');

    const listadoIngredientes = ingredientsData.data
      .map(item => `- ${getDisplayName(item.name)}: ${item.quantity} unidades en bodega`)
      .join('\n');

    const resumenComprasInventario = ingredientsData.data.map(ingredient => {
      const nombre = getDisplayName(ingredient.name);
      const cantidadEnBodega = ingredient.quantity;
      const compraDelDia = stockData.data.find(item => item.name === ingredient.name);
      const cantidadComprada = compraDelDia ? compraDelDia.total_quantity : 0;
      return `- ${nombre}: ${cantidadEnBodega} unidades en bodega — ${cantidadComprada} unidades compradas hoy`;
    }).join('\n');

    const prompt = `
Eres un asesor experto en compras para restaurantes y siempre debes responder en español.

A continuación tienes el resumen de los platillos preparados el día ${today}:
${listadoPlatillos}

Compras realizadas en el día:
${listadoStock}

Inventario actual en bodega:
${listadoIngredientes}

Resumen combinado de compras e inventario actual:
${resumenComprasInventario}

Por favor, analiza todos estos datos y responde:
- Si un ingrediente tiene pocas unidades en bodega, menciona si se compró hoy o no.
- Si se compró y sigue bajo, sugiere comprar más o revisar la rotación.
- Si no se compró y está bajo, sugiere comprar.
- Si está bien de inventario, indica que no es necesario.
- Da recomendaciones claras y amigables como si fueras un asesor experto.
- Al final, incluye un pequeño resumen en formato de lista con las recomendaciones.

ATENCIÓN:
- No confundas ingredientes con platillos.
- Los productos del inventario son INGREDIENTES o INSUMOS, no platillos.
- Los platillos son las recetas vendidas y son distintos a los ingredientes.
- Si aparece algún nombre de producto o palabra en inglés, TRADÚCELA al español.
- No uses kilos ni litros, solo unidades o cantidades.

Ejemplo de respuesta:
“¡Hola Soy Bot-Chef!  
Veo que las papas con queso se han vendido bastante y tienes poco inventario de papas, te recomiendo comprar 30 unidades para no quedarte sin stock.  
El arroz está bien abastecido, así que no es necesario comprar más por ahora.  
También te sugiero revisar el inventario de tomate a mitad de semana, ya que suele moverse rápido.  

Resumen:  
- Papas: 30 unidades  
- Arroz: Sin compra necesaria  
- Tomate: Revisar a mitad de semana”
`;

    const response = await axios.post(GROQ_URL, {
      model: "llama3-8b-8192",
      messages: [{ role: "user", content: prompt }],
    }, {
      headers: {
        'Authorization': `Bearer ${API_KEY}`,
        'Content-Type': 'application/json',
      },
    });

    res.json({
      fecha: today,
      recomendacion: response.data.choices[0].message.content.trim(),
    });

  } catch (error) {
    console.error('❌ Error:', error.message);
    res.status(500).json({ error: 'Error al obtener datos o generar la recomendación' });
  }
});

app.listen(3000, () => {
  console.log('✅ API escuchando en http://localhost:3000');
});
