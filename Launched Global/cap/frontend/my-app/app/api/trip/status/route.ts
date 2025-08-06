// file: app/api/trip/status/route.ts
import { NextResponse } from 'next/server';

let tripStatus: Record<number, 'upcoming' | 'ongoing' | 'completed'> = {};

export async function POST(req: Request) {
  const body = await req.json();
  const { bookingId, action } = body;

  if (!bookingId || !['start', 'end'].includes(action)) {
    return NextResponse.json({ error: 'Invalid request' }, { status: 400 });
  }

  if (action === 'start') {
    tripStatus[bookingId] = 'ongoing';
  } else if (action === 'end') {
    tripStatus[bookingId] = 'completed';
  }

  return NextResponse.json({ bookingId, status: tripStatus[bookingId] });
}

