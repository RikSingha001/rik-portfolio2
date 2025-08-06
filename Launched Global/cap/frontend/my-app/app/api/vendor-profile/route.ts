// file: app/api/vendor-profile/route.ts
import { NextRequest, NextResponse } from 'next/server';
import mysql from 'mysql2/promise';
import { connectToDatabase } from '@/lib/db';

export async function POST(req: NextRequest) {
  try {
    const data = await req.json();
    const {
      email,
      new_password,
      confirm_password,
      phone,
      vehicleType,
      vehicleNumber,
      vehicleModel,
      availability,
      vehicleInsurance,
      condition_check_status
    } = data;

    if (!email) {
      return NextResponse.json({ message: 'Email is required' }, { status: 400 });
    }

    const fieldsToUpdate: string[] = [];
    const values: any[] = [];

    // Optional password change
    if (new_password && confirm_password) {
      if (new_password !== confirm_password) {
        return NextResponse.json({ message: 'Passwords do not match' }, { status: 400 });
      }
      const hashedPassword = await (new_password);
      fieldsToUpdate.push('password = ?');
      values.push(hashedPassword);
    }

    if (phone) {
      fieldsToUpdate.push('phone = ?');
      values.push(phone);
    }

    if (vehicleType) {
      fieldsToUpdate.push('vehicleType = ?');
      values.push(vehicleType);
    }

    if (vehicleNumber) {
      fieldsToUpdate.push('vehicleNumber = ?');
      values.push(vehicleNumber);
    }

    if (vehicleModel) {
      fieldsToUpdate.push('vehicleModel = ?');
      values.push(vehicleModel);
    }

    if (availability) {
      fieldsToUpdate.push('availability = ?');
      values.push(availability);
    }

    if (vehicleInsurance) {
      fieldsToUpdate.push('vehicleInsurance = ?');
      values.push(vehicleInsurance);
    }

    if (condition_check_status) {
      fieldsToUpdate.push('condition_check_status = ?');
      values.push(condition_check_status);
    }

    if (fieldsToUpdate.length === 0) {
      return NextResponse.json({ message: 'No update fields provided' }, { status: 400 });
    }

    values.push(email); // For WHERE clause
    const query = `UPDATE vendor_labor SET ${fieldsToUpdate.join(', ')} WHERE email = ?`;

    const connection = await connectToDatabase();
    const [rows]: any = await connection.execute(query, values);
    await connection.end();

    if (rows.affectedRows === 0) {
      return NextResponse.json({ message: 'No record updated. Check email.' }, { status: 404 });
    }

    return NextResponse.json({ message: 'Profile updated successfully' }, { status: 200 });
  } catch (error) {
    console.error('❌ Error in vendor-profile API:', error);
    return NextResponse.json({ message: 'Internal server error' }, { status: 500 });
  }
}
