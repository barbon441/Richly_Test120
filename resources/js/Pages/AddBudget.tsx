import axios from "axios";
import { useState } from "react";

const AddBudget = () => {
    const [category, setCategory] = useState("");
    const [amount, setAmount] = useState("");
    const [startDate, setStartDate] = useState("");
    const [endDate, setEndDate] = useState("");

    const handleSubmit = async () => {
        try {
            const response = await axios.post("/budgets", {
                category_id: category,
                amount_limit: amount,
                start_date: startDate,
                end_date: endDate,
            });

            console.log("✅ งบประมาณถูกบันทึก:", response.data);
            alert("เพิ่มงบประมาณสำเร็จ!");
        } catch (error) {
            console.error("❌ Error:", error.response?.data || error.message);
        }
    };

    return (
        <div>
            <h2>เพิ่มงบประมาณ</h2>
            <input type="text" placeholder="หมวดหมู่" value={category} onChange={(e) => setCategory(e.target.value)} />
            <input type="number" placeholder="จำนวนเงิน" value={amount} onChange={(e) => setAmount(e.target.value)} />
            <input type="date" value={startDate} onChange={(e) => setStartDate(e.target.value)} />
            <input type="date" value={endDate} onChange={(e) => setEndDate(e.target.value)} />
            <button onClick={handleSubmit}>บันทึก</button>
        </div>
    );
};

export default AddBudget;
