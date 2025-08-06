import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '@/lib/db';
import { RowDataPacket } from 'mysql2';

interface Booking extends RowDataPacket {
  id: number;
  travel_date: string;
  driver_name: string;
  vehicle_type: string;
  status: string;
  vehicle_number: string;
  guest_name: string;
  guest_contact: string;
  guest_location: string;
  company_name: string;
  reference_name: string;
  trip: string;
  invoice_number: string;
  pickup_time: string;
  drop_time: string;
  assoc_vendor: string;
  op_km: string;
  total_km: string;
  toll_parking: string;
  night: string;
  total_amount: string;
  fuel_office: string;
  fuel_cash: string;
  road_tax: string;
  expenses: string;
  adv_office: string;
  location_link: string;
}

export default async function handler(
  req: NextApiRequest,
  res: NextApiResponse
) {
  let connection;

  try {
    connection = await connectToDatabase();

    const [rows] = await connection.execute<Booking[]>(
      'SELECT * FROM bookings ORDER BY id DESC'
    );

    res.status(200).json(rows);
  } catch (err: any) {
    console.error('Error fetching bookings:', err);
    res.status(500).json({
      message: 'Database error',
      error: err.message || err,
    });
  } finally {
    if (connection) {
      try {
        await connection.end();
      } catch (endErr) {
        console.error('Error closing DB connection:', endErr);
      }
    }
  }
}
