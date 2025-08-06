'use client';

import { useState } from 'react';
import React from 'react';


export default function RegisterPage() {
  const [role, setRole] = useState('');
  
  const handleRoleChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    setRole(e.target.value);
  };
  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.target as HTMLFormElement);
    const data = Object.fromEntries(formData);
    console.log(data);
    try {
      const response = await fetch('/api/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
      });
      if (response.ok) {
        const responseData = await response.json();
        console.log(responseData);
        window.location.href = '/login';
        // if(role=='driver'){
        //   window.location.href = '/driver-login';
        // }
       
        // else if(role=='user'){
        //   window.location.href = '/login';
        // }
      } 
    } catch (error) {
      console.error('Error:', error);
      alert('Something went wrong');
    }
  };

  return (
   <main>
     <div className="register-container" style={{ border: '1px solid #ccc', padding: '20px', margin: '20px' }}>

      <form action="" action-type="'submit'" onSubmit={handleSubmit} method="POST">
        <label htmlFor="name">Name:</label>
        <input
          type="text"
          id="name"
          name="name"
          placeholder="Enter your full name"
          title="Full Name"
          required
        />
        <br />
        <label htmlFor="email">Email:</label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder="Enter your email"
          title="Email Address"
          required
        />  
        <br />

        <label htmlFor="password">Password:</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Enter a password"
          title="Password"
          required
        />
        <br />    
        <label htmlFor="phone">Mobile Number:</label>
        <input
          type="text"
          id="phone"
          name="phone"
          placeholder="Enter your mobile number"
          title="Mobile Number"
          required
        />
          <br />
        <label htmlFor="aadhar">Aadhar Number:</label>
        <input
          type="text"
          id="aadhar"
          name="aadhar_number"
          placeholder="Enter your Aadhar number"
          title="Aadhar Number"
          required
        />
        <br />
        {/* <label htmlFor="role">Select Role:</label>
        <select
          id="role"
          name="role"
          value={role}
          onChange={handleRoleChange}
          title="Select your role"
          required
        >
          <option value="">-- Select Role --</option>
          <option value="user">User</option>
          <option value="driver">Driver</option>
          
        </select>
              <br />
        {(role === 'driver' ) && (
          <div>
            <label htmlFor="pan">PAN Number:</label>
            <input
              type="text"
              id="pan"
              name="pan_number"
              placeholder="Enter PAN number"
              title="PAN Number"
              required
            />
            <br />          
            <label htmlFor="license">License Number:</label>
            <input
              type="text"
              id="license"
              name="license_number"
              placeholder="Enter license number"
              title="License Number"
              required
            />
            <br />          
            <label htmlFor="vehicleNumber">Vehicle Number:</label>
            <input
              type="text"
              id="vehicleNumber"
              name="vehicle_number"
              placeholder="Enter vehicle number"
              title="Vehicle Number"
              required
            />
            <br />
            <label htmlFor="vehicleModel">Vehicle Model:</label>
            <input
              type="text"
              id="vehicleModel"
              name="vehicle_model"
              placeholder="Enter vehicle model"
              title="Vehicle Model"
              required
            />
            <br />
            <label htmlFor="vehicleUsage">Vehicle sit Type:</label>
            <input
              type="text"
              id="vehicleUsage"
              name="vehicle_usage"
              placeholder="Enter sit type (e.g., personal/commercial)"
              title="Vehicle Usage"
              required
            />
            <br />

            <label htmlFor="vehicleMileage">Vehicle Mileage (km/l):</label>
            <input
              type="number"
              step="0.1"
              id="vehicleMileage"
              name="vehicle_mileage"
              placeholder="Enter mileage (e.g., 18.5)"
              title="Vehicle Mileage"
              required
            />
          </div>
        )} */}


        <br />
        <button type="submit"   title="Register your account" 
        >Register</button>
      </form>
      
    </div>
    </main>
  );
}
