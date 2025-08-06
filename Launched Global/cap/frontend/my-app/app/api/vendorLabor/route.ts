import { NextRequest, NextResponse } from 'next/server';
import { connectToDatabase } from '@/lib/db';

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    console.log('📦 Received Body:', body);
    const connection = await connectToDatabase();

    const {
      employeeID,
      driver_name,
      date_of_join,
      password,
      vehicleType,
      vehicleNumber,
      vehicleModel,
      availability,
      condition_check_status,
      vehicleInsurance,
      panNumber,
      aadharNumber,
      licenseNumber,
      phoneNumber,
      email,
      address,
      salary,
      department,
      bankAccountNumber,
      ifscCode,
      owner_name,
      company_email,
      company_licence,
      company_address,
      company_name,
      company_contact
    } = body;

    const [result] = await connection.execute(
  `INSERT INTO vendor_labor (
    employeeID, driver_name, date_of_join, password,
    vehicleType, vehicleNumber, vehicleModel,
    availability, condition_check_status, vehicleInsurance,
    panNumber, aadharNumber, licenseNumber,
    phoneNumber, email, address,
    salary, department, bankAccountNumber, ifscCode,
    owner_name, company_email, company_licence,
    company_address, company_name, company_contact
  ) VALUES (?, ?, ?, ?, ?, ?, ?, 
  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
  [
    employeeID, driver_name, date_of_join, password,
    vehicleType, vehicleNumber, vehicleModel,
    availability, condition_check_status, vehicleInsurance,
    panNumber, aadharNumber, licenseNumber,
    phoneNumber, email, address,
    salary, department, bankAccountNumber, ifscCode,
    owner_name, company_email, company_licence,
    company_address, company_name, company_contact
  ]
);

  

    await connection.end();

    return NextResponse.json({ success: true, message: 'Vendor labor registered successfully', result });
  } catch (error) {
    console.error('❌ Server Error:', error);
    return NextResponse.json({ success: false, error: 'Internal Server Error' }, { status: 500 });
  }
}
 