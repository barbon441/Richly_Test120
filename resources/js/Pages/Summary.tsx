import { PieChart, Pie, Cell, Tooltip, Legend } from "recharts";
import { useEffect, useState } from "react";
import { Link } from "@inertiajs/react";

// ✅ สีของแต่ละหมวดหมู่
const COLORS = ["#FF6384", "#36A2EB", "#FFCE56", "#4CAF50", "#FF9800", "#9C27B0"];

type TransactionSummary = {
    category: string;
    total: number;
    color: string;
};

const Summary = () => {
    const [data, setData] = useState<TransactionSummary[]>([]);
    const [type, setType] = useState<"expense" | "income">("expense"); // ✅ ค่าเริ่มต้นเป็น "รายจ่าย"
    
    useEffect(() => {
        const token = localStorage.getItem("token"); // ✅ ดึง Token ของผู้ใช้ที่ล็อกอิน
        fetch(`/api/summary?type=${type}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                Authorization: `Bearer ${token}` // ✅ ส่ง Token ไปให้ API
            },
        })
            .then((response) => {
                console.log("📥 API Response:", response);
                return response.json();
            })
            .then((result) => {
                console.log(`📥 ข้อมูลที่ได้รับจาก API (${type}):`, result);

                // ✅ เช็คว่า API ส่ง error message หรือไม่
                if (!Array.isArray(result)) {
                    console.warn("⚠️ API ส่งค่าผิดพลาด:", result);
                    setData([]); // ล้างข้อมูลถ้า API ส่ง error
                    return;
                }

                // ✅ จัดรูปแบบข้อมูล
                const chartData = result.map((item: any, index: number) => ({
                    category: item.category || "ไม่ระบุ",
                    total: Math.abs(item.total),
                    color: COLORS[index % COLORS.length],
                }));

                setData(chartData);
            })
            .catch((error) => {
                console.error("❌ โหลดข้อมูลล้มเหลว:", error);
                setData([]); // ล้างข้อมูลเมื่อเกิดข้อผิดพลาด
            });
    }, [type]);


    return (
        <div className="flex flex-col items-center justify-center min-h-screen bg-amber-100 p-6">
            {/* ✅ กรอบขาวใหญ่ขึ้น ครอบทั้งปุ่มกลับ, Dropdown และกราฟ */}
            <div className="bg-white p-10 rounded-xl shadow-lg w-full max-w-5xl relative">
                {/* 🔙 ปุ่มย้อนกลับ */}
                <Link
                    href="/dashboard"
                    className="absolute top-4 left-4 bg-amber-300 text-white px-4 py-2 rounded-lg shadow-md hover:bg-amber-500 transition duration-300"
                >
                    🔙
                </Link>

                {/* 🔹 หัวข้อกลางด้านบน */}
                <h2 className="text-2xl font-bold text-gray-800 flex items-center justify-center mt-4">
                    {type === "expense" ? "💸 สรุปรายจ่าย" : "💰 สรุปรายรับ"}
                </h2>

                {/* 🔽 Dropdown สำหรับเลือกประเภท */}
                <div className="flex justify-center items-center mt-4">
                    <label className="text-lg font-bold text-gray-700 mr-3">ดูข้อมูล:</label>
                    <select
                        className="border border-gray-300 p-2 rounded-lg bg-white text-gray-700 shadow-md focus:ring-2 focus:ring-amber-500 focus:outline-none appearance-none px-4 pr-8"
                        value={type}
                        onChange={(e) => setType(e.target.value as "expense" | "income")}
                    >
                        <option value="expense">รายจ่าย</option>
                        <option value="income">รายรับ</option>
                    </select>
                </div>

                {/* ✅ กราฟวงกลม */}
                <div className="flex justify-center mt-6">
                    <PieChart width={400} height={400}>
                        <Pie
                            data={data}
                            cx="50%"
                            cy="50%"
                            outerRadius={150} // ✅ ขนาดสมดุล
                            fill="#8884d8"
                            dataKey="total"
                            nameKey="category"
                            label
                        >
                            {data.map((entry, index) => (
                                <Cell key={`cell-${index}`} fill={entry.color} />
                            ))}
                        </Pie>
                        <Tooltip />
                        <Legend />
                    </PieChart>
                </div>
            </div>
        </div>
    );
};

export default Summary;
