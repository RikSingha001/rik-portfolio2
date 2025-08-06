
import React,{useState} from "react";
import Link from "next/link";

export default function DotHome() {
   const [open, setOpen] = useState(false);

  return (<main >
      
    <div className="flex justify-end p-4 bg-white relative">
      
      {/* Three dot button */}
      <button type="button"
        onClick={() => setOpen(!open)}
        // className="px-3 py-2 rounded-full bg-gray-200 text-black hover:bg-gray-300 text-xl font-bold"
        // title="Menu"
      >
        <span className="sr-only">Open menu</span>
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
     

      {/* Dropdown menu */}
      {open && (
        <div className="absolute top-full right-0 mt-2 w-48 bg-white shadow-lg border border-gray-200 rounded-md z-50">
          <div className="flex flex-col p-2 gap-2">
          
            
            <Link href="/userProfileSession">
              <button type="button" className="px-4 py-2 bg-red-700 text-white rounded hover:bg-red-800">Profile</button>
            </Link>
            <Link href="/upcomingRiding">
              <button type="button" className="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Upcoming Rides</button>
            </Link>
            <Link href="/previousRiding">
              <button type="button" className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Previous booking</button>
            </Link>
          </div>
        </div>
      )}
        {/* <UpcomingRiding />
        <PreviousRiding />
        <UserProfileSession /> */}
        <button type="button"onClick={() => window.location.href = '/login'} > Logout</button>
      </div>
    </main>
  );
}