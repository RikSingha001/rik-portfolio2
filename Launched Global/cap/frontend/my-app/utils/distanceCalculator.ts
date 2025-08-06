// utils/distanceCalculator.ts
export async function calculateDistance(pickup: string, drop: string): Promise<string | null> {
  const apiKey = process.env.NEXT_PUBLIC_GOOGLE_MAPS_API_KEY;

  if (!apiKey) {
    console.error('Google Maps API key is missing.');
    return null;
  }

  const url = `https://maps.googleapis.com/maps/api/distancematrix/json?origins=${encodeURIComponent(pickup)}&destinations=${encodeURIComponent(drop)}&key=${apiKey}`;

  try {
    const res = await fetch(url);
    const data = await res.json();

    if (data.rows[0].elements[0].status === 'OK') {
      return data.rows[0].elements[0].distance.text; // e.g., "56.4 km"
    } else {
      console.warn('Distance API error:', data.rows[0].elements[0].status);
      return null;
    }
  } catch (error) {
    console.error('Distance fetch error:', error);
    return null;
  }
}
