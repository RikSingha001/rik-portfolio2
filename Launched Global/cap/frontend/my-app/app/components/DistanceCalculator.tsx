'use client';
import { useState } from 'react';
import { Input } from '@/components/input';
import { Button } from '@/components/button';



export default function DistanceCalculator() {
  const [pickup, setPickup] = useState('');
  const [drop, setDrop] = useState('');
  const [distance, setDistance] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const handleCalculate = async () => {
    if (!pickup || !drop) return alert('Please enter both pickup and drop locations.');
    setLoading(true);

    try {
      const res = await fetch(`/api/getDistance?pickup=${encodeURIComponent(pickup)}&drop=${encodeURIComponent(drop)}`);
      const data = await res.json();

      if (res.ok && data.distance) {
        setDistance(data.distance); // like "25.2 km"
      } else {
        setDistance(null);
        alert(data.error || 'Could not calculate distance');
      }
    } catch (err) {
      console.error('Fetch error:', err);
      alert('Something went wrong');
    }

    setLoading(false);
  };

  return (
    <div className="p-6 max-w-xl mx-auto space-y-4">
      <h2 className="text-xl font-bold">Distance Calculator</h2>

      <input
        className="border rounded p-2 w-full"
        placeholder="Enter Pickup Location"
        value={pickup}
        onChange={(e) => setPickup(e.target.value)}
      />

      <input
        className="border rounded p-2 w-full"
        placeholder="Enter Drop Location"
        value={drop}
        onChange={(e) => setDrop(e.target.value)}
      />

      <button
        className="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:opacity-50"
        onClick={handleCalculate}
        disabled={loading}
      >
        {loading ? 'Calculating...' : 'Calculate Distance'}
      </button>

      {distance && (
        <p className="mt-4 text-green-600 font-medium">
          Distance: {distance}
        </p>
      )}
    </div>
  );
}
