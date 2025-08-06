// pages/api/distance.ts
import type { NextApiRequest, NextApiResponse } from 'next';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  const { pickup, drop } = req.query;

  // Input validation
  if (!pickup || !drop) {
    return res.status(400).json({ error: 'Missing pickup or drop location' });
  }

  const apiKey = process.env.GOOGLE_MAPS_API_KEY;

  if (!apiKey) {
    return res.status(500).json({ error: 'Google Maps API key not set in environment variables' });
  }

  const url = `https://maps.googleapis.com/maps/api/distancematrix/json?origins=${encodeURIComponent(
    pickup as string
  )}&destinations=${encodeURIComponent(drop as string)}&key=${apiKey}`;

  try {
    const response = await fetch(url);
    const data = await response.json();

    const distance = data?.rows?.[0]?.elements?.[0]?.distance?.value; // meters

    res.status(200).json({
      distanceInKm: distance ? distance / 1000 : null, // convert to KM
      raw: data, // optional: for debugging
    });
  } catch (err) {
    console.error('Distance API error:', err);
    res.status(500).json({ error: 'Failed to fetch distance' });
  }
}
