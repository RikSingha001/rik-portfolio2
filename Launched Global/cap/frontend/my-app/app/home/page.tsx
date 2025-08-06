
'use client';
import { calculateDistance } from '@/utils/distanceCalculator'
import { useState, useEffect } from 'react';
import DotHome from '@/components/DotHome';
import MapLocationForm from '@/components/MapLocationForm';
export default function HomePage() {
  const [form, setForm] = useState({
    pickup_location: '',
    drop_location: '',
    travel_date: '',
    pickup_time: '',
    drop_time: '',
    number_of_sit: '',
    role: '',
  });

  const [distance, setDistance] = useState('');

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const getDistance = async (pickup: string, drop: string) => {
    try {
      const res = await fetch(`/api/getDistance?pickup=${pickup}&drop=${drop}`);
      const data = await res.json();
      if (data.distance) {
        setDistance(data.distance);
        console.log('Distance:', data.distance);
      } else {
        setDistance('');
        console.error('Distance error:', data.error || data);
      }
    } catch (err) {
      console.error('Fetch error:', err);
      setDistance('');
    }
  };




  useEffect(() => {
    if (form.pickup_location && form.drop_location) {
      getDistance(form.pickup_location, form.drop_location);
    }
  }, [form.pickup_location, form.drop_location]);

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    try {
      const response = await fetch('/api/book', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(form),
      });
      const result = await response.json();
      if (response.ok) {
        alert('Booking successful!');
        window.location.href = '/upcomingRiding';
      } else {
        alert(result.message || 'Booking failed');
      }
    } catch (err) {
      console.error('Submit error:', err);
      alert('Error while submitting form');
    }
  };

  const handleDistance = async () => {
    if (form.pickup_location && form.drop_location) {
      const distance = await calculateDistance(form.pickup_location, form.drop_location);
      setDistance(distance || '');
    }
  };

  return (
    <main>
      <DotHome />
      <MapLocationForm  />
      <div className="container mt-5">
        <h1>Book a Ride</h1>
        <form onSubmit={handleSubmit}>
          <div className="row mb-3">
            <div className="col-md-6">
              <label>Pickup Location</label>
              <input
                name="pickup_location"
                value={form.pickup_location}
                onChange={handleChange}
                required
                className="form-control"
              />
            </div>
            <div className="col-md-6">
              <label>Drop Location</label>
              <input
                name="drop_location"
                value={form.drop_location}
                onChange={handleChange}
                required
                className="form-control"
              />
            </div>
          </div>

          <div className="row mb-3">
            <div className="col-md-6">
              <label>Date of Travel</label>
              <input
                type="date"
                name="travel_date"
                value={form.travel_date}
                onChange={handleChange}
                required
                className="form-control"
              />
            </div>
            <div className="col-md-6">
              <label>Pickup Time</label>
              <input
                type="time"
                name="pickup_time"
                value={form.pickup_time}
                onChange={handleChange}
                required
                className="form-control"
              />
            </div>
          </div>

          <div className="row mb-3">
            <div className="col-md-6">
              <label>Number of Seats</label>
              <input
                type="number"
                name="number_of_sit"
                value={form.number_of_sit}
                onChange={handleChange}
                required
                className="form-control"
              />
            </div>
            <div className="col-md-6">
              <label>Drop Time</label>
              <input
                type="time"
                name="drop_time"
                value={form.drop_time}
                onChange={handleChange}
                required
                className="form-control"
              />
            </div>
          </div>

          <div className="mb-3">
            <label>Role</label>
            <select
              name="role"
              value={form.role}
              onChange={handleChange}
              required
              className="form-control"
            >
              <option value="">-- Select Role --</option>
              <option value="drivers">Self-driver</option>
              <option value="vendor">Company-associated Vendor</option>
            </select>
          </div>

          {distance && (
            <div className="alert alert-info mt-3">
              Estimated Distance: <strong>{distance}</strong>
            </div>
          )}

          <button type="submit" className="btn btn-primary">
            Book Ride
          </button>
        </form>
      </div>
    </main>
  );
}
