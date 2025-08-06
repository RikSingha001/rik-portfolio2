// ✅ file: components/MapLocationForm.tsx
'use client';

import React, { useState } from 'react';
import Script from 'next/script';

interface FormValues {
  pickup_location: string;
  drop_location: string;
  distance: string;
}

export default function MapLocationForm() {
  const [form, setForm] = useState<FormValues>({
    pickup_location: '',
    drop_location: '',
    distance: '',
  });

  const handlePlaceChange = (field: 'pickup_location' | 'drop_location', value: string) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const initAutocomplete = () => {
    const autocompletePickup = new google.maps.places.Autocomplete(
      document.getElementById('pickup') as HTMLInputElement
    );
    const autocompleteDrop = new google.maps.places.Autocomplete(
      document.getElementById('drop') as HTMLInputElement
    );

    autocompletePickup.addListener('place_changed', () => {
      const place = autocompletePickup.getPlace();
      if (place.formatted_address) handlePlaceChange('pickup_location', place.formatted_address);
    });

    autocompleteDrop.addListener('place_changed', () => {
      const place = autocompleteDrop.getPlace();
      if (place.formatted_address) handlePlaceChange('drop_location', place.formatted_address);
    });
  };

  const calculateDistance = async () => {
    const res = await fetch(
      `https://maps.googleapis.com/maps/api/distancematrix/json?origins=${form.pickup_location}&destinations=${form.drop_location}&key=${process.env.NEXT_PUBLIC_GOOGLE_MAPS_API_KEY}`
    );
    const data = await res.json();
    const distance = data.rows[0]?.elements[0]?.distance?.text || 'Unknown';
    setForm((prev) => ({ ...prev, distance }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    await calculateDistance();
    alert(`Distance: ${form.distance}`);
    // Here you can add DB submission logic with distance
  };

  return (
    <main className="p-6">
      <Script
        src={`https://maps.googleapis.com/maps/api/js?key=${process.env.NEXT_PUBLIC_GOOGLE_MAPS_API_KEY}&libraries=places`}
        onLoad={initAutocomplete}
      />

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label htmlFor="pickup" className="form-label">Pickup Location</label>
          <input 
          onChange={(e) => handlePlaceChange('pickup_location', e.target.value)}
          id="pickup" type="text" className="form-control" required />
        </div>
        <div>
          <label htmlFor="drop" className="form-label">Drop Location</label>
          <input 
           name="drop_location" 
          onChange={(e) => handlePlaceChange('drop_location', e.target.value)}
          id="drop" type="text" className="form-control" required />
        </div>
        <button type="submit" className="btn btn-primary">Calculate Distance</button>

        {form.distance && (
          <p className="mt-3">Distance: <strong>{form.distance}</strong></p>
        )}
      </form>
    </main>
  );
}
