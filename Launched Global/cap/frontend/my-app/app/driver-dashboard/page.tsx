// app/driver-dashboard/page.tsx
'use client';
import React from 'react'
import DotDrivers from '@/components/DotDrivers';

export default function DriverDashboardPage() {
  return (<main><DotDrivers />
    <main className="flex min-h-screen flex-col items-center justify-between p-24">

      <div >
        <h1 className="text-4xl font-bold">Driver Dashboard</h1>
        <p className="mt-4">Welcome to your dashboard!</p>
        {/* Add more dashboard features here */}
      </div>
    </main>
    </main>
  )
}