// file: pages/api/vendor-registration.ts
import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '../../lib/db';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Method Not Allowed' });
  }

  try {
    const {
      owner_name,
      company_email,
      company_licence,
      company_address,
      company_name,
    } = req.body;

    if (!owner_name || !company_email || !company_licence || !company_address || !company_name) {
      return res.status(400).json({ message: 'All fields are required' });
    }

    const db = await connectToDatabase();

    // 🔁 Check if vendor already exists
    const [existing]: any = await db.execute(
      'SELECT id FROM vendor_registration WHERE company_email = ? OR company_licence = ?',
      [company_email, company_licence]
    );

    if (existing.length > 0) {
      return res.status(409).json({ message: 'Vendor already exists' });
    }

    // ✅ Insert new vendor
    const [result]: any = await db.execute(
      `INSERT INTO vendor_registration (
        owner_name,
        company_name,
        company_email,
        company_licence,
        company_address
      ) VALUES (?, ?, ?, ?, ?)`,
      [owner_name, company_name, company_email, company_licence, company_address]
    );

    return res.status(201).json({
      message: 'Vendor registered successfully',
      id: result.insertId,
    });

  } catch (error: any) {
    console.error('Vendor registration error:', error);
    return res.status(500).json({ message: 'Internal Server Error' });
  }
}
