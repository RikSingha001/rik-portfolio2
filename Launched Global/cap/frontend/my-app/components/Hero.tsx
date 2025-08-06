import Image from "next/image";

const Hero = () => {
  return (
    <div className="middle overflow-hidden">
      <video src="\lestoo.mp4" className="w-full h-auto object-cover"
        autoPlay
        muted
        loop
        playsInline
      />
      
       
    </div>
  );
};{/* /* <Image
        src="/open.jpg"
        alt="Hero Image"
        width={1200}
        height={600}
        className="w-full h-auto object-cover"
      /> */ }

export default Hero;
