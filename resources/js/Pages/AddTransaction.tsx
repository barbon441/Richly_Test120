import { useState } from "react";
import { createTransaction } from "@/services/transactionService";
import { useCategories } from "@/hooks/useCategories";
import CategorySelect from "@/Components/CategorySelect";
import Keypad from "@/Components/Keypad";

const AddTransaction = () => {
    const [amount, setAmount] = useState("");
    const [note, setNote] = useState("");
    const [transactionType, setTransactionType] = useState<"expense" | "income">("expense");
    const [category, setCategory] = useState<number | null>(null);
    const categories = useCategories(transactionType);
    const [loading, setLoading] = useState(false);

    const handleSubmit = async () => {
        if (!amount || !category) return alert("❌ กรุณากรอกจำนวนเงินและเลือกหมวดหมู่");

        setLoading(true);
        try {
            await createTransaction({
                category_id: category,
                amount: transactionType === "expense" ? `-${Math.abs(Number(amount))}` : `${Math.abs(Number(amount))}`,
                transaction_type: transactionType,
                description: note,
                transaction_date: new Date().toISOString().split("T")[0],
            });
            window.location.href = "/dashboard";
        } catch (error) {
            alert("❌ เกิดข้อผิดพลาด: " + (error as Error).message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className=" min-h-screen bg-amber-50 pt-16 flex flex-col items-center">
            {/* Header */}
            <div className="bg-amber-400 text-white p-4 flex justify-between items-center shadow-md w-full fixed top-0 left-0 z-50">
                <button onClick={() => history.back()} className="text-xl">🔙</button>
                <h2 className="text-lg font-semibold">{transactionType === "expense" ? "เพิ่มรายจ่าย" : "เพิ่มรายรับ"}</h2>
            </div>

            {/* ปุ่มเลือก รายจ่าย/รายรับ */}
            <div className="flex justify-center mt-4 w-full max-w-5xl">
                <button
                    onClick={() => setTransactionType("income")}
                    className={`px-6 py-3 mx-2 rounded-lg shadow-md text-lg font-semibold ${
                        transactionType === "income" ? "bg-green-400 text-white" : "bg-gray-200 text-gray-700"
                    }`}
                >
                    รายรับ 💰
                </button>
                <button
                    onClick={() => setTransactionType("expense")}
                    className={`px-6 py-3 mx-2 rounded-lg shadow-md text-lg font-semibold ${
                        transactionType === "expense" ? "bg-red-400 text-white" : "bg-gray-200 text-gray-700"
                    }`}
                >
                    รายจ่าย 💸
                </button>
            </div>

            {/* เลือกหมวดหมู่ */}
            <CategorySelect categories={categories} selected={category} onSelect={setCategory} />

            {/* รายละเอียดธุรกรรม */}
            <div className="bg-white p-6 rounded-lg shadow-lg mx-4 mt-4 w-full max-w-5xl">
                <h3 className="text-lg font-semibold text-gray-700 mb-2">รายละเอียดธุรกรรม</h3>
                <div className="grid grid-cols-2 gap-4">
                    <input
                        type="text"
                        value={amount}
                        onChange={(e) => setAmount(e.target.value)}
                        className="w-full p-4 text-3xl text-center bg-amber-100 rounded-lg"
                        placeholder="฿0.00"
                    />
                    <input
                        type="text"
                        value={note}
                        onChange={(e) => setNote(e.target.value)}
                        className="w-full p-4 text-lg bg-amber-100 rounded-lg"
                        placeholder="รายละเอียดเพิ่มเติม..."
                    />
                </div>
            </div>

            {/* แป้นพิมพ์ตัวเลข */}
            <div className="bg-amber-200 text-black p-6 mt-6 rounded-lg shadow-lg w-full max-w-5xl">
                <Keypad onPress={(key) => setAmount((prev) => prev + key)} onDelete={() => setAmount((prev) => prev.slice(0, -1))} />
            </div>

            {/* ปุ่ม ลบ และ บันทึก */}
            <div className="grid grid-cols-2 gap-3 mt-3 w-full max-w-5xl">
                <button
                    onClick={() => setAmount("")}
                    className="p-4 rounded-lg text-2xl font-semibold bg-red-500 hover:bg-red-600 text-white"
                >
                    ← ลบ
                </button>
                <button
                    onClick={handleSubmit}
                    className="p-4 rounded-lg text-2xl font-semibold bg-green-500 hover:bg-green-600 text-white"
                >
                    ✅ บันทึก
                </button>
            </div>
        </div>
    );
};

export default AddTransaction;
