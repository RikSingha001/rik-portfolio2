import Image from "next/image"; 
export default function Head() {
  return(
    <main>
      <div className="middle overflow-hidden">
          <video src="\welcome.mp4" className="w-full h-auto object-cover"
        autoPlay
        muted
        loop
        playsInline
      />
      </div>
    </main>
  );
}