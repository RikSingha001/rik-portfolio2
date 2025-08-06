'use client';
import React, { useEffect, useState } from 'react';
import './allBookings.css';

interface Booking {
  id: number;
  travel_date: string;
  driver_name: string;
  vehicle_type: string;
  status: string;
  vehicle_number: string;
  guest_name: string;
  guest_contact: string;
  guest_location: string;
  company_name: string;
  reference_name: string;
  trip: string;
  invoice_number: string;
  pickup_time: string;
  drop_time: string;
  assoc_vendor: string;
  op_km: string;
  total_km: string;
  toll_parking: string;
  night: string;
  total_amount: string;
  fuel_office: string;
  fuel_cash: string;
  road_tax: string;
  expenses: string;
  adv_office: string;
  location_link: string;
}

export default function AllBookingsPage() {
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [search, setSearch] = useState('');

  useEffect(() => {
    fetch('/api/bookings')
      .then((res) => res.json())
      .then((data) => setBookings(data))
      .catch((err) => console.error('Error fetching bookings:', err));
  }, []);

  const filteredBookings = bookings.filter(
    (booking) =>
      booking.guest_name?.toLowerCase().includes(search.toLowerCase()) ||
      booking.company_name?.toLowerCase().includes(search.toLowerCase()) ||
      booking.trip?.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <main>
      <div className="container mt-4"> 
        <h1 className="mb-4">All Bookings</h1>
 
        <div className="mb-4">
          <input
            type="text"
            className="form-control"
            placeholder="Search by guest, company or trip..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
          />
        </div>

        <div className="row">
          {filteredBookings.map((booking) => (
            <div className="col-md-6 col-lg-4 mb-4" key={booking.id}>
              <div className="card h-100 shadow">
                <div className="card-body overflow-auto" style={{ maxHeight: '500px' }}>
                  <h5 className="card-title">Booking #{booking.id}</h5>
                  <p><strong>Date:</strong> {booking.travel_date}</p>
                  <p><strong>Status:</strong> <span className={`badge ${getStatusClass(booking.status)}`}>{booking.status}</span></p>
                  <p><strong>Driver:</strong> {booking.driver_name}</p>
                  <p><strong>Vehicle:</strong> {booking.vehicle_type} ({booking.vehicle_number})</p>
                  <hr />
                  <p><strong>Guest:</strong> {booking.guest_name}</p>
                  <p><strong>Contact:</strong> {booking.guest_contact}</p>
                  <p><strong>Location:</strong> {booking.guest_location}</p>
                  <p><strong>Reference:</strong> {booking.reference_name}</p>
                  <p><strong>Company:</strong> {booking.company_name}</p>
                  <p><strong>Trip:</strong> {booking.trip}</p>
                  <p><strong>Invoice:</strong> {booking.invoice_number}</p>
                  <p><strong>Pickup:</strong> {booking.pickup_time}</p>
                  <p><strong>Drop:</strong> {booking.drop_time}</p>
                  <p><strong>OP KM:</strong> {booking.op_km}</p>
                  <p><strong>Total KM:</strong> {booking.total_km}</p>
                  <p><strong>Toll & Parking:</strong> {booking.toll_parking}</p>
                  <p><strong>Night:</strong> {booking.night}</p>
                  <p><strong>Total Amount:</strong> ₹{booking.total_amount}</p>
                  <p><strong>Fuel Office:</strong> {booking.fuel_office}</p>
                  <p><strong>Fuel Cash:</strong> {booking.fuel_cash}</p>
                  <p><strong>Road Tax:</strong> {booking.road_tax}</p>
                  <p><strong>Expenses:</strong> {booking.expenses}</p>
                  <p><strong>Advance Office:</strong> {booking.adv_office}</p>
                  <p><strong>Location Link:</strong> <a href={booking.location_link} target="_blank">{booking.location_link}</a></p>
                  <p><strong>Vendor:</strong> {booking.assoc_vendor}</p>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </main>
  );
}

// Badge Color Utility
function getStatusClass(status: string) {
  switch (status) {
    case 'completed':
      return 'bg-success text-white';
    case 'ongoing':
      return 'bg-warning text-dark';
    case 'cancelled':
      return 'bg-danger text-white';
    default:
      return 'bg-secondary text-white';
  }
}
