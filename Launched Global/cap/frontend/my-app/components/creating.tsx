import React from 'react';
import Image from 'next/image';
import Link from 'next/link';

export default function Creating () {
  return(
    <main>
      <div className="mid-body">
        <h1>Creating Component</h1>
        <img src =" rik.jpg" alt ="RIK SINGHA" width={50} height={60}
        /> 
        <h1 className="text-2xl font-bold mb-4">About This Project</h1>
        <p className="mb-2">
          This website was developed by <strong>Rik Singha</strong> as part of an internship project under <strong>Launched Global</strong>.
        </p>
        <p className="mb-2">
          I have independently designed and developed the complete project — including frontend, backend, data collection, and notification system.
        </p>
        <p className="mb-2">
          If you’re interested in collaborating on any project, feel free to reach out!
        </p>
        <p className="mb-2">
          🔗 <Link href="https://github.com/RikSingha001" target="_blank" className="text-blue-600 underline">GitHub: RikSingha001</Link>
          <br />
          📧 <Link href="mailto:riksingha420@gmail.com" className="text-blue-600 underline">Email: riksingha420@gmail.com</Link>
        </p>
      </div>
    </main>
  )
}
