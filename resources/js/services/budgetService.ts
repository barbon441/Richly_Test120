import axios from "axios";

// ✅ ฟังก์ชันดึง Token จาก localStorage
const getAuthHeaders = () => {
    const token = localStorage.getItem("token");
    return token ? { Authorization: `Bearer ${token}` } : {};
};

// ✅ ฟังก์ชันสร้างงบประมาณ (Create)
export const createBudget = async (data: { user_id: number; category_id: number; amount: number }) => {
    return await axios.post("/api/budgets", data, { headers: getAuthHeaders() });
};

// ✅ ฟังก์ชันดึงรายการงบประมาณ (Read)
export const getBudgets = async () => {
    return await axios.get("/api/budgets", { headers: getAuthHeaders() });
};

// ✅ ฟังก์ชันอัปเดตงบประมาณ (Update)
export const updateBudget = async (id: number, amount: number) => {
    return await axios.put(`/api/budgets/${id}`, { amount }, { headers: getAuthHeaders() });
};

// ✅ ฟังก์ชันลบงบประมาณ (Delete)
export const deleteBudget = async (id: number) => {
    return await axios.delete(`/api/budgets/${id}`, { headers: getAuthHeaders() });
};
