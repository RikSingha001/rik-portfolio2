// file: app/vendor/manual-booking/page.tsx


// file: app/vendor/manual-booking/page.tsx
'use client';
import { useState } from 'react';
import { Input } from '@/components/input';
import { Button } from '@/components/button';

interface Booking {
  id: number;
  guestName: string;
  pickup: string;
  drop: string;
  date: string;
  time: string;
  vehicleType: string;
}

export default function ManualBooking() {
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [form, setForm] = useState<Omit<Booking, 'id'>>({
    guestName: '',
    pickup: '',
    drop: '',
    date: '',
    time: '',
    vehicleType: '',
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const createBooking = () => {
    setBookings([...bookings, { id: Date.now(), ...form }]);
    setForm({ guestName: '', pickup: '', drop: '', date: '', time: '', vehicleType: '' });
  };

  return (
    <div className="p-6 max-w-2xl mx-auto">
      <h2 className="text-xl font-bold mb-4">Create Manual Booking</h2>
      <div className="grid gap-2">
        <Input name="guestName" placeholder="Guest Name" value={form.guestName} onChange={handleChange} />
        <Input name="pickup" placeholder="Pickup Location" value={form.pickup} onChange={handleChange} />
        <Input name="drop" placeholder="Drop Location" value={form.drop} onChange={handleChange} />
        <Input name="date" type="date" value={form.date} onChange={handleChange} />
        <Input name="time" type="time" value={form.time} onChange={handleChange} />
        <Input name="vehicleType" placeholder="Vehicle Type" value={form.vehicleType} onChange={handleChange} />
        <Button onClick={createBooking}>Create Booking</Button>
      </div>

      <h3 className="mt-6 font-semibold text-lg">Upcoming Manual Bookings</h3>
      <ul className="mt-2 space-y-1">
        {bookings.map(booking => (
          <li key={booking.id} className="border p-2 rounded shadow">
            <div className="font-medium">{booking.guestName} - {booking.vehicleType}</div>
            <div className="text-sm">{booking.pickup} → {booking.drop} on {booking.date} at {booking.time}</div>
          </li>
        ))}
      </ul>
    </div>
  );
}
