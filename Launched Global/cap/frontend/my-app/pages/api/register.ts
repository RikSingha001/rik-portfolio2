// import type { NextApiRequest, NextApiResponse } from 'next';
// import { connectToDatabase } from '../../lib/db';

// export default async function handler(req: NextApiRequest, res: NextApiResponse) {
//   if (req.method !== 'POST') {
//     return res.status(405).json({ message: 'Only POST requests are allowed' });
//   }

//   const {
//     name, 
//     email,
//     password,
//     phone,
//     aadhar_number
    
//   } = req.body;

//   // ✅ Common validation
//   if (!name || !email || !password || !phone || !aadhar_number || ) {
//     return res.status(400).json({ message: 'Missing required common fields' });
//   }

//   const db = await connectToDatabase(); // ✅ move db connection to top

//   // ✅ Driver & Vendor validation
//   // if (
//   //   (role === 'driver' || role === 'vendor') &&
//   //   (!pan_number ||
//   //    !license_number ||
//   //    !vehicle_number ||
//   //    !vehicle_model ||
//   //    !vehicle_usage ||
//   //    !vehicle_mileage)
//   // ) {
//   //   return res.status(400).json({ message: 'Missing required driver/vendor fields' });
//   // }

//   // ✅ Vendor-only validation
//   // if (role === 'vendor') {
//   //   if (!company_name || !company_email || !company_licence || !company_address) {
//   //     return res.status(400).json({ message: 'Company details are required for association' });
//   //   }

   

   
//   }

//   try {
//     // Step 0: Check if email already exists
//     const [existingUser]: any = await db.execute(
//       'SELECT id FROM users WHERE email = ?',
//       [email]
//     );

//     if (existingUser.length > 0) {
//       return res.status(409).json({ message: 'Email already registered' });
//     }

//     // Step 1: Insert into users table
//     const [userResult]: any = await db.execute(
//       `INSERT INTO users (name, email, password, phone, aadhar_number, role)
//        VALUES (?, ?, ?, ?, ?, ?)`,
//       [name, email, password, phone, aadhar_number, role]
//     );

//     const userId = userResult.insertId;

//     // Step 2: Insert into drivers table
//     // if (role === 'driver') {
//     //   await db.execute(
//     //     `INSERT INTO drivers (
//     //       user_id, name, email, password, phone, aadhar_number, pan_number, license_number,
//     //       vehicle_number, vehicle_model, vehicle_usage, vehicle_mileage
//     //     ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
//     //     [
//     //       userId,
//     //       name, email, password, phone, aadhar_number,
//     //       pan_number, license_number, vehicle_number,
//     //       vehicle_model, vehicle_usage, vehicle_mileage,
//     //     ]
//     //   );
//     // }

    

    
//   } catch (err) {
//     console.error('Registration error:', err);
//     return res.status(500).json({ message: 'Internal Server Error' });
//   }
import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '../../lib/db';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Only POST requests are allowed' });
  }

  const {
    name,
    email,
    password,
    phone,
    aadhar_number,
    
  } = req.body;

  // ✅ Validate common user fields
  if (!name || !email || !password || !phone || !aadhar_number ) {
    return res.status(400).json({ message: 'Missing required user fields' });
  }


  try {
    const db = await connectToDatabase();

    // ✅ Check if email already registered
    const [existingUser]: any = await db.execute(
      'SELECT id FROM users WHERE email = ?',
      [email]
    );

    if (existingUser.length > 0) {
      return res.status(409).json({ message: 'Email already registered' });
    }

    // ✅ Insert into users table
    const [userResult]: any = await db.execute(
      `INSERT INTO users (name, email, password, phone, aadhar_number)
       VALUES (?, ?, ?, ?, ?)`,
      [name, email, password, phone, aadhar_number]
    );

    return res.status(201).json({ message: 'User registered successfully', userId: userResult.insertId });
  } catch (err) {
    console.error('Registration error:', err);
    return res.status(500).json({ message: 'Internal Server Error' });
  }
}
