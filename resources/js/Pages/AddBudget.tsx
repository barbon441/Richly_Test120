import { useState } from "react";
import axios from "axios";

// ✅ ประกาศ BudgetType
type BudgetType = {
    id: number;
    category_id: number;
    category_name?: string; // อาจจะไม่มีค่ากรณีเพิ่มใหม่
    amount_limit: number;
    amount_spent?: number; // อาจจะไม่มีค่ากรณีเพิ่มใหม่
    start_date: string;
    end_date: string;
};
type AddBudgetProps = {
    onClose: () => void;
    onBudgetAdded: () => void;
    budget?: BudgetType | null;
};

const AddBudget: React.FC<AddBudgetProps> = ({ onClose, onBudgetAdded, budget }) => {
    const [categoryId, setCategoryId] = useState<number | "">(budget?.category_id || "");
    const [amount, setAmount] = useState<number | "">(budget?.amount_limit || "");
    const [startDate, setStartDate] = useState(budget?.start_date || "");
    const [endDate, setEndDate] = useState(budget?.end_date || "");

    const handleSubmit = async () => {
        try {
            const budgetData = {
                category_id: Number(categoryId),
                amount_limit: Number(amount),
                start_date: startDate,
                end_date: endDate,
            };

            if (budget?.id) {
                // ✅ อัปเดตงบประมาณ
                await axios.put(`/api/budgets/${budget.id}`, budgetData);
                alert("✅ อัปเดตงบประมาณสำเร็จ!");
            } else {
                // ✅ เพิ่มงบประมาณใหม่
                await axios.post("/api/budgets", budgetData);
                
            }

            onBudgetAdded();
            onClose();
        } catch (error) {
            console.error("❌ Error:", error);
        }
    };
    return (
        <div className="bg-white p-6 rounded-lg shadow-lg max-w-lg">
            <h2 className="text-xl font-bold mb-4">เพิ่มงบประมาณ</h2>
            <select
                value={categoryId}
                onChange={(e) => setCategoryId(Number(e.target.value))}
                className="border p-2 rounded w-full mb-2"
            >
                <option value="">เลือกหมวดหมู่</option>
                <option value="1">อาหาร</option>
                <option value="2">เดินทาง</option>
                <option value="3">ที่อยู่อาศัย</option>
                <option value="4">ของใช้</option>
                <option value="5">อื่นๆ</option>
            </select>
            <input
                type="number"
                placeholder="จำนวนเงิน"
                value={amount}
                onChange={(e) => setAmount(Number(e.target.value))}
                className="border p-2 rounded w-full mb-2"
            />
            <input
                type="date"
                value={startDate}
                onChange={(e) => setStartDate(e.target.value)}
                className="border p-2 rounded w-full mb-2"
            />
            <input
                type="date"
                value={endDate}
                onChange={(e) => setEndDate(e.target.value)}
                className="border p-2 rounded w-full mb-4"
            />
            <button onClick={handleSubmit} className="px-4 py-2 bg-amber-500 text-white rounded-md w-full">
                บันทึก
            </button>
            <button onClick={onClose} className="px-4 py-2 bg-gray-300 text-gray-700 rounded-md w-full mt-2">
                ❌ ปิด
            </button>
        </div>
    );
};

export default AddBudget;
