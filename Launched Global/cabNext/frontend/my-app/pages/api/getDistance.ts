// pages/api/getDistance.ts
import type { NextApiRequest, NextApiResponse } from 'next';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  const { pickup, drop } = req.query;

  if (!pickup || !drop) {
    return res.status(400).json({ error: 'Missing pickup or drop' });
  }

  const apiKey = process.env.NEXT_PUBLIC_GOOGLE_MAPS_API_KEY;

  try {
    const url = `https://maps.googleapis.com/maps/api/distancematrix/json?origins=${pickup}&destinations=${drop}&key=${apiKey}`;
    const response = await fetch(url);
    const data = await response.json();

    if (data?.rows?.[0]?.elements?.[0]?.status === 'OK') {
      const distance = data.rows[0].elements[0].distance.text;
      return res.status(200).json({ distance });
    } else {
      return res.status(400).json({ error: 'Distance not found', raw: data });
    }
  } catch (error) {
    return res.status(500).json({ error: 'API error', details: error });
  }
}
