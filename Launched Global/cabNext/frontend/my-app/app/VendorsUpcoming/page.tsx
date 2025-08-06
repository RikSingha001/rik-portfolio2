'use client';
import React, { useEffect, useState } from 'react';

interface Ride {
  id: number;
  pickup_location: string;
  drop_location: string;
  travel_date: string;
  pickup_time: string;
  status: string;
  assoc_vendor: string;
}

export default function VendorsUpcomingPage() {
  const [ride, setRide] = useState<Ride | null>(null);
  const vendorId = 'vendor_123'; // Replace with session storage or auth context

  const fetchRide = async () => {
    const res = await fetch(`/api/vendorUpcomingRide?vendorId=${vendorId}`);
    const data = await res.json();
    setRide(data.ride || null);
  };

  useEffect(() => {
    fetchRide();
  }, []);

  const handleAction = async (action: 'start' | 'end' | 'cancel') => {
    const res = await fetch(`/api/${action}Trip`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ booking_id: ride?.id, vendor_id: vendorId }),
    });
    const result = await res.json();
    alert(result.message);
    if (result.success) fetchRide(); // reload ride info
  };

  return (
    <main className="p-6">
      <h1 className="text-2xl font-bold mb-4">Vendor's Upcoming Ride</h1>

      {ride ? (
        <div className="bg-white p-4 rounded shadow">
          <p><strong>From:</strong> {ride.pickup_location}</p>
          <p><strong>To:</strong> {ride.drop_location}</p>
          <p><strong>Date:</strong> {ride.travel_date}</p>
          <p><strong>Time:</strong> {ride.pickup_time}</p>
          <p><strong>Status:</strong> {ride.status}</p>

          <div className="mt-4 space-x-4">
            <button type ="button" className="bg-red-600 text-white px-4 py-2 rounded" onClick={() => handleAction('cancel')}>Cancel</button>
            <button type ="button" className="bg-yellow-500 text-white px-4 py-2 rounded" onClick={() => handleAction('start')}>Start</button>
            <button type ="button" className="bg-green-600 text-white px-4 py-2 rounded" onClick={() => handleAction('end')}>End</button>
          </div>
        </div>
      ) : (
        <p>No upcoming rides.</p>
      )}
    </main>
  );
}
