'use client';
import React, { useState } from 'react';
export default function DriversProfilePage() {

     const [role, setRole] = useState('');
        const handleRoleChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
          setRole(e.target.value);
        };
      return(
    
        <div className="flex justify-center items-center h-screen">
          <h1>Forgot Password</h1>
          <form>
            <select
              id="role"
              name="role"
              value={role}
              onChange={handleRoleChange}
              title="Select your role"
              required
            >
              <option value="">-- Select Role --</option>
              <option value="password">password</option>
              <option value="phone">phone</option>
              <option value="email">email</option>
            </select>

            {(role === 'password' ) && (
              <div >          <label htmlFor="new-password">New Password:</label>
            <input type="password" id="new-password" name="new-password" placeholder="enter new password"  required />
            <label htmlFor="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your new password" required />
           </div>
            )}
            <br/>
            {(role === 'phone')&&(
              <div >
                <label htmlFor="phone">enter new phone number </label>
                <input type ="number" id="phone" name="phone" placeholder="Enter your new phone" required />
              </div>
            )}
            <br/>
            {(role === 'email')&&(
              <div >
                <label htmlFor="email">enter new email id </label>
                <input type ="email" id="email" name="email" placeholder="Enter your new email" required />
              </div>
            )}

            <button type="submit">Submit</button>
          </form>
        </div>

      );
}
           