'use client';
import AllBookingsPage from '../allbooking/page'
import OpenMarketPage from '../open-market/page'
import DriverManagement from '../driver-management/page';
import ManualBooking from '../manual-booking/page';
import React, { useState } from 'react';

export default function VendorsProfilePage() {
  const [role, setRole] = useState('');

  const handleRoleChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    setRole(e.target.value);
  };

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    const formData = new FormData(e.currentTarget);
    const data = Object.fromEntries(formData.entries());

    try {
      const response = await fetch('/api/vendor-profile', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
      });

      const responseData = await response.json();

      if (!response.ok) {
        alert(responseData.message || 'Update failed');
        return;
      }

      alert('Update success!');
      console.log('✅ Updated:', responseData);
    } catch (error) {
      console.error('❌ Error:', error);
      alert('Something went wrong');
    }
  };

  return (<main>
  <AllBookingsPage />
  <OpenMarketPage />
 
  <ManualBooking />
    <div className="flex justify-center items-center h-screen bg-gray-100">
      <form onSubmit={handleSubmit} className="bg-white p-8 rounded shadow-md w-full max-w-md space-y-4">
        <h1 className="text-2xl font-bold text-center">Update Information</h1>

        <label htmlFor="role" className="block font-medium">Choose Option</label>
        <select
          id="role"
          name="role"
          value={role}
          onChange={handleRoleChange}
          className="w-full border border-gray-300 rounded px-3 py-2"
          required
        >
          <option value="">-- Select Role --</option>
          <option value="password">Change Password</option>
          <option value="phone">Update Phone</option>
          <option value="email">Update Email</option>
          <option value="vehicle">Change Vehicle Info</option>
        </select>

        {role === 'password' && (
          <div className="space-y-2">
            <label htmlFor="new_password" className="block">New Password</label>
            <input type="password" id="new_password" name="new_password" className="input" placeholder="Enter new password" required />
            <label htmlFor="confirm_password" className="block">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" className="input" placeholder="Confirm password" required />
          </div>
        )}

        {role === 'phone' && (
          <div className="space-y-2">
            <label htmlFor="phone" className="block">New Phone Number</label>
            <input type="tel" id="phone" name="phone" className="input" placeholder="Enter phone number" required />
          </div>
        )}

        {role === 'email' && (
          <div className="space-y-2">
            <label htmlFor="email" className="block">New Email</label>
            <input type="email" id="email" name="email" className="input" placeholder="Enter email" required />
          </div>
        )}

        {role === 'vehicle' && (
          <div className="space-y-2">
            <label htmlFor="vehicleType" className="block">Vehicle Type</label>
            <input type="text" id="vehicleType" name="vehicleType" className="input" placeholder="Vehicle type" required />

            <label htmlFor="vehicleNumber" className="block">Vehicle Number</label>
            <input type="text" id="vehicleNumber" name="vehicleNumber" className="input" placeholder="Vehicle number" required />

            <label htmlFor="vehicleModel" className="block">Vehicle Model</label>
            <input type="text" id="vehicleModel" name="vehicleModel" className="input" placeholder="Vehicle model" required />

            <label htmlFor="availability" className="block">Availability</label>
            <input type="text" id="availability" name="availability" className="input" placeholder="Availability status" required />

            <label htmlFor="vehicleInsurance" className="block">Vehicle Insurance</label>
            <input type="text" id="vehicleInsurance" name="vehicleInsurance" className="input" placeholder="Insurance details" required />

            <label htmlFor="condition_check_status" className="block">Condition Check Status</label>
            <input type="text" id="condition_check_status" name="condition_check_status" className="input" placeholder="Condition check status" required />
          </div>
        )}

        <button type="submit" className="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Submit</button>
      </form>
    </div>
    </main>
  );
}
