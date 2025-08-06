import { connectToDatabase } from '@/lib/db';

export async function POST(request: Request) {
  try {
    const connection = await connectToDatabase();
    const [rows] = await connection.execute('SELECT * FROM bookings ORDER BY id DESC');
    await connection.end();

    return new Response(JSON.stringify(rows), {
      status: 200,
      headers: { 'Content-Type': 'application/json' },
    });
  } catch (err) {
    return new Response(JSON.stringify({ message: 'DB error', error: err }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' },
    });
  }
}
