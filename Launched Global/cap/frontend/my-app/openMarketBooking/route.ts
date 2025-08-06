import { NextRequest, NextResponse } from 'next/server';
import { connectToDatabase } from '@/lib/db';

// ✅ GET - fetch all open market requests
export async function GET(req: NextRequest) {
  try {
    const db = await connectToDatabase();

    const [rows] = await db.execute(
      `SELECT * FROM open_market_requests WHERE status = 'open' ORDER BY release_time DESC`
    );

    return NextResponse.json({ requests: rows });
  } catch (error) {
    console.error('GET /openMarketBooking error:', error);
    return NextResponse.json({ success: false, error: 'Failed to fetch requests' }, { status: 500 });
  }
}

// ✅ POST - add a new open market request
export async function POST(req: NextRequest) {
  try {
    const { booking_id, vendor_id, region } = await req.json();

    const db = await connectToDatabase();

    const releaseTime = new Date();

    await db.execute(
      `INSERT INTO open_market_requests (booking_id, released_by, region, release_time, status, whitelisted_only)
       VALUES (?, ?, ?, ?, 'open', 1)`,
      [booking_id, vendor_id, region, releaseTime]
    );

    return NextResponse.json({ success: true, message: 'Request added to Open Market' });
  } catch (error) {
    console.error('POST /openMarketBooking error:', error);
    return NextResponse.json({ success: false, error: 'Failed to add request' }, { status: 500 });
  }
}
