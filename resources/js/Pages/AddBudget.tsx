import { useState, useEffect } from "react";
import axios from "axios";
import { Inertia } from "@inertiajs/inertia";

const AddBudget = () => {
    const [amount, setAmount] = useState("");
    const [category, setCategory] = useState("");
    const [categories, setCategories] = useState<{ id: number; name: string }[]>([]);
    const [startDate, setStartDate] = useState("");  // ✅ เพิ่มวันที่เริ่มต้น
    const [endDate, setEndDate] = useState("");      // ✅ เพิ่มวันที่สิ้นสุด



    // โหลดหมวดหมู่จาก API
    useEffect(() => {
        const fetchCategories = async () => {
            try {
                const token = localStorage.getItem("auth_token");

                if (!token) {
                    console.warn("⚠ ไม่มี Token ใน localStorage, ข้ามการโหลดหมวดหมู่");
                    return; // ❌ ไม่โหลด API ถ้าไม่มี Token
                }

                const response = await axios.get("/api/categories", {
                    headers: { Authorization: `Bearer ${token}` }
                });

                console.log("📥 หมวดหมู่ที่ได้รับจาก API:", response.data);
                setCategories(response.data);
            } catch (error) {
                console.error("❌ ไม่สามารถโหลดหมวดหมู่ได้", error);
            }
        };

        fetchCategories();
    }, []);






    // ฟังก์ชันบันทึกงบประมาณ
    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        if (!amount || !category) {
            alert("⚠ กรุณากรอกข้อมูลให้ครบถ้วน");
            return;
        }

        try {
            const token = localStorage.getItem("auth_token");
            if (!token) {
                alert("❌ กรุณาเข้าสู่ระบบก่อน");
                return;
            }

            const response = await axios.post("/api/budgets", {
                category_id: category,
                amount_limit: parseFloat(amount),
                start_date: startDate,  // ✅ ส่งค่า start_date
                end_date: endDate       // ✅ ส่งค่า end_date
            }, {
                headers: { Authorization: `Bearer ${token}` }
            });

            console.log("✅ งบประมาณถูกบันทึกแล้ว", response.data);
            alert("✅ บันทึกสำเร็จ!");
            Inertia.visit('/dashboard');  // ✅ กลับไปหน้าหลัก
        } catch (error) {
            console.error("❌ เกิดข้อผิดพลาด", error);
            alert("❌ ไม่สามารถบันทึกงบประมาณได้");
        }
    };

    return (
        <div className="min-h-screen bg-amber-50 flex flex-col items-center">
            <div className="bg-amber-400 text-white w-full p-4 text-center text-lg font-semibold shadow-md">
                เพิ่มงบประมาณ
            </div>

            <form onSubmit={handleSubmit} className="bg-white p-6 mt-6 rounded-lg shadow-md w-full max-w-md">
                <h3 className="text-lg font-semibold text-gray-700 mb-4">เลือกหมวดหมู่</h3>
                <select
                    className="w-full p-3 border rounded-lg bg-gray-100"
                    value={category}
                    onChange={(e) => setCategory(e.target.value)}
                >
                    <option value="">-- เลือกหมวดหมู่ --</option>
                    {categories.map((cat) => (
                        <option key={cat.id} value={cat.id}>
                            {cat.name}
                        </option>
                    ))}
                </select>

                <h3 className="text-lg font-semibold text-gray-700 mt-4 mb-2">กำหนดจำนวนเงิน</h3>
                <input
                    type="number"
                    value={amount}
                    onChange={(e) => setAmount(e.target.value)}
                    className="w-full p-3 text-lg text-center border rounded-lg bg-amber-100"
                    placeholder="฿0.00"
                />

                {/* ✅ เพิ่มฟิลด์เลือกวันที่ */}
                <h3 className="text-lg font-semibold text-gray-700 mt-4 mb-2">วันที่เริ่มต้น</h3>
                <input
                    type="date"
                    value={startDate}
                    onChange={(e) => setStartDate(e.target.value)}
                    className="w-full p-3 text-lg text-center border rounded-lg bg-gray-100"
                />

                <h3 className="text-lg font-semibold text-gray-700 mt-4 mb-2">วันที่สิ้นสุด</h3>
                <input
                    type="date"
                    value={endDate}
                    onChange={(e) => setEndDate(e.target.value)}
                    className="w-full p-3 text-lg text-center border rounded-lg bg-gray-100"
                />

                <button
                    type="submit"
                    className="mt-4 w-full p-3 text-lg font-semibold bg-green-500 hover:bg-green-600 text-white rounded-lg"
                >
                    ✅ บันทึกงบประมาณ
                </button>
            </form>
        </div>
    );
};

export default AddBudget;
