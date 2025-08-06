'use client';
import { GoogleMap, LoadScript, Marker } from '@react-google-maps/api';

const containerStyle = {
  width: '100%',
  height: '400px'
};

export default function LocationMap({ center }: { center: { lat: number; lng: number } }) {
  return (
    <LoadScript googleMapsApiKey={process.env.NEXT_PUBLIC_GOOGLE_MAPS_API_KEY!}>
      <GoogleMap mapContainerStyle={containerStyle} center={center} zoom={12}>
        <Marker position={center} />
      </GoogleMap>
    </LoadScript>
  );
}
