'use client';
import React, { useState } from 'react';

export default function VendorsProfilePage() {
  const [role, setRole] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const handleRoleChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    setRole(e.target.value);
  };

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setIsLoading(true);

    const formData = new FormData(e.currentTarget);
    const data = Object.fromEntries(formData.entries());

    // Add the current vendor's email (you might want to get this from session/context)
    // For now, assuming it comes from the form
    if (!data.email && role !== 'email') {
      alert('Email is required to identify your account');
      setIsLoading(false);
      return;
    }

    try {
      const response = await fetch('/api/vendor-profile', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          ...data,
          role: undefined // Remove role from data as it's not needed in API
        }),
      });

      const responseData = await response.json();

      if (!response.ok) {
        alert(responseData.message || 'Update failed');
        setIsLoading(false);
        return;
      }

      alert('Profile updated successfully!');
      console.log('✅ Updated:', responseData);
      
      // Reset form
      e.currentTarget.reset();
      setRole('');
      
    } catch (error) {
      console.error('❌ Error:', error);
      alert('Something went wrong. Please try again.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <main>
      <div className="flex justify-center items-center min-h-screen bg-gray-100 p-4">
        <form 
          onSubmit={handleSubmit} 
          className="bg-white p-8 rounded-lg shadow-md w-full max-w-md space-y-4"
        >
          <h1 className="text-2xl font-bold text-center text-gray-800">Update Profile</h1>

          {/* Email field - always required to identify the vendor */}
          <div className="space-y-2">
            <label htmlFor="current_email" className="block font-medium text-gray-700">
              Your Current Email <span className="text-red-500">*</span>
            </label>
            <input 
              type="email" 
              id="current_email" 
              name="email" 
              className="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
              placeholder="Enter your current email"
              required 
            />
          </div>

          <div className="space-y-2">
            <label htmlFor="role" className="block font-medium text-gray-700">
              What would you like to update? <span className="text-red-500">*</span>
            </label>
            <select
              id="role"
              name="role"
              value={role}
              onChange={handleRoleChange}
              className="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            >
              <option value="">-- Select What to Update --</option>
              <option value="password">Change Password</option>
              <option value="phone">Update Phone Number</option>
              <option value="vehicle">Update Vehicle Information</option>
            </select>
          </div>

          {role === 'password' && (
            <div className="space-y-4 border-t pt-4">
              <h3 className="font-medium text-gray-700">Password Change</h3>
              <div className="space-y-2">
                <label htmlFor="new_password" className="block text-sm text-gray-600">
                  New Password <span className="text-red-500">*</span>
                </label>
                <input 
                  type="password" 
                  id="new_password" 
                  name="new_password" 
                  className="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                  placeholder="Enter new password (min 8 characters)"
                  minLength={8}
                  required 
                />
              </div>
              <div className="space-y-2">
                <label htmlFor="confirm_password" className="block text-sm text-gray-600">
                  Confirm New Password <span className="text-red-500">*</span>
                </label>
                <input 
                  type="password" 
                  id="confirm_password" 
                  name="confirm_password" 
                  className="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                  placeholder="Confirm your new password"
                  minLength={8}
                  required 
                />
              </div>
            </div>
          )}

          {role === 'phone' && (
            <div className="space-y-4 border-t pt-4">
              <h3 className="font-medium text-gray-700">Phone Number Update</h3>
              <div className="space-y-2">
                <label htmlFor="phone" className="block text-sm text-gray-600">
                  New Phone Number <span className="text-red-500">*</span>
                </label>
                <input 
                  type="tel" 
                  id="phone" 
                  name="phone" 
                  className="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                  placeholder="Enter phone number (e.g., +1234567890)"
                  required 
                />
              </div>
            </div>
          )}

          {role === 'vehicle' && (
            <div className="space-y-4 border-t pt-4">
              <h3 className="font-medium text-gray-700">Vehicle Information Update</h3>
              
              <div className="space-y-2">
                <label htmlFor="vehicleType" className="block text-sm text-gray-600">Vehicle Type</label>
                <input 
                  type="text" 
                  id="vehicleType" 
                  name="vehicleType" 
                  className="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                  placeholder="e.g., Truck, Van, Car"
                />
              </div>

              <div className="space-y-2">
                <label htmlFor="vehicleNumber" className="block text-sm text-gray-600">Vehicle Number</label>
                <input 
                  type="text" 
                  id="vehicleNumber" 
                  name="vehicleNumber" 
                  className="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                  placeholder="e.g., ABC-1234"
                />
              </div>

              <div className="space-y-2">
                <label htmlFor="vehicleModel" className="block text-sm text-gray-600">Vehicle Model</label>
                <input 
                  type="text" 
                  id="vehicleModel" 
                  name="vehicleModel" 
                  className="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                  placeholder="e.g., Toyota Camry 2020"
                />
              </div>

              <div className="space-y-2">
                <label htmlFor="availability" className="block text-sm text-gray-600">Availability Status</label>
                <select 
                  id="availability" 
                  name="availability" 
                  className="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">-- Select Availability --</option>
                  <option value="available">Available</option>
                  <option value="unavailable">Unavailable</option>
                  <option value="busy">Busy</option>
                </select>
              </div>

              <div className="space-y-2">
                <label htmlFor="vehicleInsurance" className="block text-sm text-gray-600">Vehicle Insurance</label>
                <input 
                  type="text" 
                  id="vehicleInsurance" 
                  name="vehicleInsurance" 
                  className="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                  placeholder="Insurance policy number or details"
                />
              </div>

              <div className="space-y-2">
                <label htmlFor="condition_check_status" className="block text-sm text-gray-600">Condition Check Status</label>
                <select 
                  id="condition_check_status" 
                  name="condition_check_status" 
                  className="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">-- Select Status --</option>
                  <option value="passed">Passed</option>
                  <option value="failed">Failed</option>
                  <option value="pending">Pending</option>
                </select>
              </div>
            </div>
          )}

          <button 
            type="submit" 
            disabled={isLoading}
            className={`w-full px-4 py-2 rounded font-medium transition-colors ${
              isLoading 
                ? 'bg-gray-400 cursor-not-allowed' 
                : 'bg-blue-600 hover:bg-blue-700 text-white'
            }`}
          >
            {isLoading ? 'Updating...' : 'Update Profile'}
          </button>
        </form>
      </div>
    </main>
  );
}