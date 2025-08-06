'use client';
import React, { useState } from 'react';
import DotVendors from '@/components/DotVendors';
import DistanceCalculator from '../components/DistanceCalculator';
import { usePathname } from 'next/navigation'
import AllBookingsPage from '../allbooking/page'
import OpenMarketPage from '../open-market/page'
import DriverManagement from '../driver-management/page';
import './vendorDashboard.css';

import ManualBooking from '../manual-booking/page';
export default function VendorDashboardPage() {
  const pathname = usePathname()
  const handleStatus = async (bookingId: number, action: 'start' | 'end') => {
  const res = await fetch('/api/trip/status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ bookingId, action })
  });
  const result = await res.json();
  console.log(result); // result.status = 'ongoing' / 'completed'
};
  return (
    <main>
       <DotVendors />
     <OpenMarketPage />
     <DistanceCalculator />
    <AllBookingsPage />
     <ManualBooking />
    <main className="flex min-h-screen flex-col items-center justify-between p-24">
      <div >
        <h1 className="text-4xl font-bold">Vendor Dashboard</h1>
        <p className="mt-4">Welcome to your dashboard!</p>
        {/* Add more dashboard features here */}
      </div>
    </main>
    </main>
  );
}