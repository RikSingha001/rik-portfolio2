import Image from "next/image";
import img from "@/public/open.jpg";
import Head from "@/components/Head";
import Hero from "@/components/Hero";
import OpenTop from "@/components/OpenTop";
import  Creating  from "@/components/creating";
export default function Home() {
  return (
    <main className="overflow-hidden min-h-screen bg-gray-50">
  <div className="Top-bottam">
    <OpenTop /> 
    <Head />
  </div> 
  <div className="right-side">
    <Hero />
    <footer>
      <div className="mid-body">
        <Creating />
      </div>
    </footer>
  </div>
   <footer className="footer">
    <p>&copy; 2025 Rik Singha. All rights reserved.</p>
    <p>📞 +91 62967 15873 | 📧 riksingha420@gmail.com | 📍 Bishnupur, Bankura, WB</p>
  </footer>
  
</main>

  );
}
