// components/LocationPicker.tsx
'use client';
import React, { useEffect, useRef } from 'react';

interface Props {
  onPlaceSelected: (address: string) => void;
  placeholder: string;
}

export default function LocationPicker({ onPlaceSelected, placeholder }: Props) {
  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (!window.google || !window.google.maps) return;

    const autocomplete = new google.maps.places.Autocomplete(inputRef.current!);

    autocomplete.addListener('place_changed', () => {
      const place = autocomplete.getPlace();
      if (place.formatted_address) {
        onPlaceSelected(place.formatted_address);
      }
    });
  }, []);

  return (
    <input
      ref={inputRef}
      type="text"
      className="form-control"
      placeholder={placeholder}
    />
  );
}
