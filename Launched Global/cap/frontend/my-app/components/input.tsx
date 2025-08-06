// components/ui/input.tsx
import React from 'react';

type InputProps = React.InputHTMLAttributes<HTMLInputElement>;

export const Input: React.FC<InputProps> = ({ className = '', ...props }) => {
  return (
    <input
      className={`border border-gray-300 px-3 py-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400 ${className}`}
      {...props}
    />
  );
};

export default Input;
