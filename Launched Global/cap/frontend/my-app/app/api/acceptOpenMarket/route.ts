import { NextRequest, NextResponse } from 'next/server';
import { connectToDatabase } from '@/lib/db';

export async function POST(req: NextRequest) {
  try {
    const { requestId, vendorId } = await req.json();

    const db = await connectToDatabase();

    const [rows]: any = await db.execute(
      'SELECT * FROM open_market_requests WHERE id = ? AND status = "open"',
      [requestId]
    );

    if (rows.length === 0) {
      return NextResponse.json({
        success: false,
        message: 'Request already accepted or not found',
      }, { status: 400 });
    }

    await db.execute(
      `UPDATE open_market_requests
       SET status = 'accepted',
           accepted_by = ?,
           accepted_at = NOW()
       WHERE id = ?`,
      [vendorId, requestId]
    );

    return NextResponse.json({
      success: true,
      message: 'Request accepted successfully',
    });
  } catch (error) {
    console.error('Accept Open Market Error:', error);
    return NextResponse.json({ success: false, error: 'Failed to accept request' }, { status: 500 });
  }
}
