// file: backend/index.js
import express from 'express';
import cors from 'cors';
import bodyParser from 'body-parser';
import dotenv from 'dotenv';
import registerRoute from './register.js';
import loginRoute from './login.js';
import driverLoginRoute from './driver-login.js';
import vendorLoginRoute from './vendor-login.js';
import vendorRegistrationRoute from './vendor_registration.js';
import bookRoute from './book.js';
const router = express.Router();
dotenv.config();

const app = express();
const PORT = process.env.PORT || 5000;
app.use(express.json());
app.use('/api', bookRoute);
// Middleware
app.use(cors());
app.use(bodyParser.json());

// Routes
app.use('/api', registerRoute);
app.use('/api', loginRoute);
app.use('/api', driverLoginRoute);
app.use('/api', vendorLoginRoute);
app.use('/api', vendorRegistrationRoute);

// Start server
app.listen(PORT, () => console.log(`🚀 Server started on port ${PORT}`));
app.listen(PORT, () => {
  console.log(`🚀 Server running on http://localhost:${PORT}`);
});