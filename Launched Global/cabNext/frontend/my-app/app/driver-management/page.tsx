// file: app/vendor/driver-management/page.tsx
'use client';
import { useState } from 'react';
import { Input } from '@/components/input';
import { Button } from '@/components/button';



interface Driver {
  id: number;
  name: string;
  phone: string;
  license: string;
  aadhar: string;
  vehicleType: string;
}

export default function DriverManagement() {
  const [drivers, setDrivers] = useState<Driver[]>([]);
  const [form, setForm] = useState<Omit<Driver, 'id'>>({
    name: '',
    phone: '',
    license: '',
    aadhar: '',
    vehicleType: '',
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const addDriver = () => {
    setDrivers([...drivers, { id: Date.now(), ...form }]);
    setForm({ name: '', phone: '', license: '', aadhar: '', vehicleType: '' });
  };

  return (
    <div className="p-6 max-w-2xl mx-auto">
      <h2 className="text-xl font-bold mb-4">Add Driver</h2>
      <div className="grid gap-2">
        <Input name="name" placeholder="Driver Name" value={form.name} onChange={handleChange} />
        <Input name="phone" placeholder="Phone Number" value={form.phone} onChange={handleChange} />
        <Input name="license" placeholder="License Number" value={form.license} onChange={handleChange} />
        <Input name="aadhar" placeholder="Aadhar Number" value={form.aadhar} onChange={handleChange} />
        <Input name="vehicleType" placeholder="Vehicle Type" value={form.vehicleType} onChange={handleChange} />
        <Button onClick={addDriver}>Add Driver</Button>
      </div>

      <h3 className="mt-6 font-semibold text-lg">Driver List</h3>
      <ul className="mt-2 space-y-1">
        {drivers.map(driver => (
          <li key={driver.id} className="border p-2 rounded shadow">
            <div className="font-medium">{driver.name} - {driver.vehicleType}</div>
            <div className="text-sm">Phone: {driver.phone} | License: {driver.license}</div>
          </li>
        ))}
      </ul>
    </div>
  );
}