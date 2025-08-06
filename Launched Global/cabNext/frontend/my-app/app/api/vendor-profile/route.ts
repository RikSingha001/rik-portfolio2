import { NextRequest, NextResponse } from 'next/server';
import mysql from 'mysql2/promise';
import bcrypt from 'bcryptjs';
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

    // Email is required to identify the vendor
    if (!email) {
      return NextResponse.json({ message: 'Email is required' }, { status: 400 });
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return NextResponse.json({ message: 'Invalid email format' }, { status: 400 });
    }

    const fieldsToUpdate: string[] = [];
    const values: any[] = [];

    // Optional password change with validation
    if (new_password && confirm_password) {
      if (new_password !== confirm_password) {
        return NextResponse.json({ message: 'Passwords do not match' }, { status: 400 });
      }
      
      // Password strength validation
      if (new_password.length < 8) {
        return NextResponse.json({ message: 'Password must be at least 8 characters long' }, { status: 400 });
      }

      const hashedPassword = await bcrypt.hash(new_password, 12);
      fieldsToUpdate.push('password = ?');
      values.push(hashedPassword);
    }

    // Validate phone number if provided
    if (phone) {
      const phoneRegex = /^\+?[\d\s\-\(\)]{10,}$/;
      if (!phoneRegex.test(phone)) {
        return NextResponse.json({ message: 'Invalid phone number format' }, { status: 400 });
      }
      fieldsToUpdate.push('phone = ?');
      values.push(phone.trim());
    }

    // Validate and sanitize vehicle information
    if (vehicleType) {
      if (vehicleType.trim().length === 0) {
        return NextResponse.json({ message: 'Vehicle type cannot be empty' }, { status: 400 });
      }
      fieldsToUpdate.push('vehicleType = ?');
      values.push(vehicleType.trim());
    }

    if (vehicleNumber) {
      if (vehicleNumber.trim().length === 0) {
        return NextResponse.json({ message: 'Vehicle number cannot be empty' }, { status: 400 });
      }
      fieldsToUpdate.push('vehicleNumber = ?');
      values.push(vehicleNumber.trim().toUpperCase());
    }

    if (vehicleModel) {
      if (vehicleModel.trim().length === 0) {
        return NextResponse.json({ message: 'Vehicle model cannot be empty' }, { status: 400 });
      }
      fieldsToUpdate.push('vehicleModel = ?');
      values.push(vehicleModel.trim());
    }

    if (availability) {
      const validAvailability = ['available', 'unavailable', 'busy'];
      if (!validAvailability.includes(availability.toLowerCase())) {
        return NextResponse.json({ 
          message: 'Availability must be one of: available, unavailable, busy' 
        }, { status: 400 });
      }
      fieldsToUpdate.push('availability = ?');
      values.push(availability.toLowerCase());
    }

    if (vehicleInsurance) {
      if (vehicleInsurance.trim().length === 0) {
        return NextResponse.json({ message: 'Vehicle insurance cannot be empty' }, { status: 400 });
      }
      fieldsToUpdate.push('vehicleInsurance = ?');
      values.push(vehicleInsurance.trim());
    }

    if (condition_check_status) {
      const validStatuses = ['passed', 'failed', 'pending'];
      if (!validStatuses.includes(condition_check_status.toLowerCase())) {
        return NextResponse.json({ 
          message: 'Condition check status must be one of: passed, failed, pending' 
        }, { status: 400 });
      }
      fieldsToUpdate.push('condition_check_status = ?');
      values.push(condition_check_status.toLowerCase());
    }

    if (fieldsToUpdate.length === 0) {
      return NextResponse.json({ message: 'No update fields provided' }, { status: 400 });
    }

    values.push(email); // For WHERE clause
    const query = `UPDATE vendor_labor SET ${fieldsToUpdate.join(', ')} WHERE email = ?`;

    const connection = await connectToDatabase();
    
    // Check if vendor exists first
    const [checkRows]: any = await connection.execute(
      'SELECT id FROM vendor_labor WHERE email = ?', 
      [email]
    );
    
    if (checkRows.length === 0) {
      await connection.end();
      return NextResponse.json({ message: 'Vendor not found with this email' }, { status: 404 });
    }

    const [rows]: any = await connection.execute(query, values);
    await connection.end();

    if (rows.affectedRows === 0) {
      return NextResponse.json({ message: 'No record updated. Please try again.' }, { status: 404 });
    }

    return NextResponse.json({ 
      message: 'Profile updated successfully',
      updatedFields: fieldsToUpdate.length 
    }, { status: 200 });

  } catch (error) {
    console.error('❌ Error in vendor-profile API:', error);
    
    // Handle specific MySQL errors
    if (error instanceof Error) {
      if (error.message.includes('Duplicate entry')) {
        return NextResponse.json({ message: 'This information already exists' }, { status: 409 });
      }
    }
    
    return NextResponse.json({ message: 'Internal server error' }, { status: 500 });
  }
}