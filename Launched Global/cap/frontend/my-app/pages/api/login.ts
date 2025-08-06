// pages/api/login.ts
import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '../../lib/db';


export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Method Not Allowed' });
  }

  const { name, phone, password } = req.body;

  if (!name || !phone || !password) {
    return res.status(400).json({ message: 'All fields are required.' });
  }

  try {
    const connection = await connectToDatabase();

    const [rows]: any = await connection.execute(
  'SELECT * FROM users WHERE name = ? AND phone = ? AND password = ?',
  [name, phone, password]
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





































  // if (req.method === 'POST') {
  //   const { name, phone, password } = req.body;

  //   console.log('Received login data:', { name, phone, password });

  //   // You can add validation or database logic here

  //   return res.status(200).json({
  //     message: 'Login successful',
  //     user: { name, phone }
  //   });
  // } else {
  //   return res.status(405).json({ message: 'Method Not Allowed' });
  // }

