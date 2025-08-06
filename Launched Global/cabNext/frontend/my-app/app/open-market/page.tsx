'use client';
import { useEffect, useState } from 'react';

interface OpenMarketRequest {
  id: number;
  booking_id: number;
  region: string;
  release_time: string;
  whitelisted_only: boolean;
}

export default function OpenMarketPage() {
  const [requests, setRequests] = useState<OpenMarketRequest[]>([]);

  useEffect(() => {
    const fetchData = async () => {
      try { 
        const res = await fetch('/api/openMarketBooking');
        if (!res.ok) throw new Error('Failed to fetch');
        const data = await res.json();
        setRequests(data.requests);
      } catch (err) {
        console.error('Error fetching requests:', err);
      }
    };
    fetchData();
  }, []);

  const acceptRequest = async (requestId: number) => {
    const vendorId = 'vendor_123'; 
    try {
      const res = await fetch('/api/acceptOpenMarket', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ requestId, vendorId }),
      });

      const result = await res.json();
      if (result.success) {
        alert('Booking accepted!');
        setRequests((prev) => prev.filter((r) => r.id !== requestId));
      } else {
        alert(result.message || 'Failed to accept');
      }
    } catch (err) {
      console.error('Accept error:', err);
      alert('Something went wrong!');
    }
  };

  return (
    <div className="p-4">
      <h2 className="text-2xl font-bold mb-4">Open Market Requests</h2>
      {requests.length === 0 && <p>No available requests right now.</p>}
      {requests.map((req) => (
        <div key={req.id} className="bg-white p-4 rounded shadow mb-3">
          <p><b>Booking ID:</b> {req.booking_id}</p>
          <p><b>Region:</b> {req.region}</p>
          <p><b>Released:</b> {new Date(req.release_time).toLocaleString()}</p>
          <p><b>Visible To:</b> {req.whitelisted_only ? 'Whitelisted Vendors Only' : 'All Vendors'}</p>
          <button type='button'
            className="mt-2 bg-blue-600 text-white px-4 py-2 rounded"
            onClick={() => acceptRequest(req.id)}
          >
            Accept Booking
          </button>
        </div>
      ))}
    </div>
  );
}
