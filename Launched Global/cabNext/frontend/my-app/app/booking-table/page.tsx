// file: app/vendor/booking-table/page.tsx
'use client';
import { useState } from 'react';
import { Button } from '@/components/button';
import { Input } from '@/components/input';

interface Booking {
  id: number;
  guestName: string;
  pickup: string;
  drop: string;
  date: string;
  status: 'upcoming' | 'ongoing' | 'completed';
}
let mockBookings: Booking[] = [
  { id: 101, guestName: 'Ravi', pickup: 'Deoghar', drop: 'Ranchi', date: '2025-07-01', status: 'upcoming' },
  { id: 102, guestName: 'Amit', pickup: 'Delhi', drop: 'Agra', date: '2025-07-03', status: 'completed' },
];

export default function BookingTable() {
  const [filter, setFilter] = useState('');
  const [data, setData] = useState(mockBookings);

  const handleStatus = async (id: number, action: 'start' | 'end') => {
    const res = await fetch('/api/trip/status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ bookingId: id, action })
    });
    const result = await res.json();
    setData(prev => prev.map(b => b.id === id ? { ...b, status: result.status } : b));
  };

  const filtered = data.filter(b =>
    b.guestName.toLowerCase().includes(filter.toLowerCase()) ||
    b.pickup.toLowerCase().includes(filter.toLowerCase()) ||
    b.drop.toLowerCase().includes(filter.toLowerCase())
  );

  return (
    <div className="p-6 max-w-4xl mx-auto">
      <h2 className="text-xl font-bold mb-4">Booking Table</h2>
      <Input
        placeholder="Search by guest, pickup or drop"
        value={filter}
        onChange={(e) => setFilter(e.target.value)}
        className="mb-4"
      />

      <table className="w-full border">
        <thead>
          <tr className="bg-gray-100">
            <th className="border p-2">ID</th>
            <th className="border p-2">Guest</th>
            <th className="border p-2">Pickup</th>
            <th className="border p-2">Drop</th>
            <th className="border p-2">Date</th>
            <th className="border p-2">Status</th>
            <th className="border p-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          {filtered.map(b => (
            <tr key={b.id} className="text-center">
              <td className="border p-2">{b.id}</td>
              <td className="border p-2">{b.guestName}</td>
              <td className="border p-2">{b.pickup}</td>
              <td className="border p-2">{b.drop}</td>
              <td className="border p-2">{b.date}</td>
              <td className={`border p-2 ${b.status === 'completed' ? 'text-green-600' : b.status === 'ongoing' ? 'text-yellow-600' : 'text-blue-600'}`}>{b.status}</td>
              <td className="border p-2 space-x-2">
                {b.status === 'upcoming' && <Button onClick={() => handleStatus(b.id, 'start')}>Start</Button>}
                {b.status === 'ongoing' && <Button onClick={() => handleStatus(b.id, 'end')}>End</Button>}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  ); 
}

