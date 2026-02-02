const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const bodyParser = require('body-parser');

const app = express();
const server = http.createServer(app);

const io = new Server(server, {
    cors: {
        origin: '*',
        methods: ['GET', 'POST']
    }
});

// Middleware para leer JSON
app.use(bodyParser.json());

// Ruta para que Laravel envíe el nuevo mensaje
app.post('/nuevo-mensaje', (req, res) => {
    const data = req.body;
    console.log('📩 POST desde Laravel:', data);
    
    // Emitimos a todos los usuarios  conectados
    io.emit('mensaje-nuevo', data);

    res.status(200).json({ status: 'ok' });
});

// Socket en tiempo real
io.on('connection', (socket) => {
    console.log('🟢 Usuario conectado:', socket.id);

    socket.on('disconnect', () => {
        console.log('🔴 Usuario desconectado:', socket.id);
    });
});

server.listen(3001, () => {
    console.log('📡 Servidor WebSocket en http://127.0.0.1:3001');
});

/*Asegúrate de que tu script escuche en la IP pública 
server.listen(3001, '0.0.0.0', () => {
    console.log('📡 Socket.IO server escuchando en http://0.0.0.0:3001');
});
*/