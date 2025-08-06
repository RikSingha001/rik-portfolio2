// login/page.tsx


"use client";
import React from 'react'; 

export default function loginPage() {
  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.target as HTMLFormElement);
    const data = Object.fromEntries(formData);
    console.log(data);
    try {
      const response = await fetch('/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json', 
  },
  body: JSON.stringify(data),
});
const responseData = await response.json();

if (!response.ok) {
  alert(responseData.message || 'Login failed');
  return;
}

console.log('Login Success:', responseData);
window.location.href = '/home';

      
    } catch (error) {
      console.error('Error:', error);
      alert('Something went wrong');
    }
  };

  return (
    <main>
      <div className="login-container" style={{ border: '1px solid #ccc ', padding: '20px', margin: '20px' }}>
        <form action='' method="POST" onSubmit={handleSubmit}>
          <label htmlFor="name">Name:</label>
          <input
            type="text"
            id="name"
            name="name"
            placeholder="Enter your full name"
            required
          />

          <label htmlFor="phone">Mobile Number:</label>
          <input
            type="text"
            id="phone"
            name="phone"
            placeholder="Enter your mobile number"
            required
          />

          <label htmlFor="password">Password:</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter a password"
            required
          />
          <br />
          <button type="submit">submit</button>
        </form>
      </div>
    </main>
  );
}
