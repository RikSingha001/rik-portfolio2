// pages/api/driver-login.ts
import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '../../lib/db';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Method Not Allowed' });
  }

  const { driver_name,vehicleNumber, phone, password, company_name } = req.body;

  if (!driver_name || !vehicleNumber || !phone || !password ||  !company_name) {
    return res.status(400).json({ message: 'All fields are required.' });
  }

  try {
    const connection = await connectToDatabase();

    
const [rows]: any = await connection.execute(
  'SELECT * FROM vendor_labor WHERE driver_name = ? AND phoneNumber = ? AND password = ? AND vehicleNumber = ? AND company_name = ?',
  [driver_name, phone, password, vehicleNumber, company_name]
);
    await connection.end();

    if (rows.length === 1) {
      return res.status(200).json({ message: 'Login successful', user: rows[0] });
    } else {
      return res.status(401).json({ message: 'Invalid credentials' });
    }
  } catch (error) {
    console.error('Login error:', error);
    return res.status(500).json({ message: 'Server error' });
  }
}





































 