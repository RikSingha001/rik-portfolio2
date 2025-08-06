import Image from "next/image";
import img from "@/public/open.jpg";
import Head from "@/components/Head";
import Hero from "@/components/Hero";
import OpenTop from "@/components/OpenTop";
import  Creating  from "@/components/creating";
export default function Home() {
  return (
    <main className="overflow-hidden">
      <div className="Top-bottam">
        <OpenTop /> 
        <Head  />
      </div> 
      <div className="right-side" >
        <Hero />
        <footer ><div className="mid-body"> <Creating /></div> </footer>
      </div>
    </main>
  );
}
