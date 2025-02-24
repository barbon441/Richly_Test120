import axios from "axios";

export const getTransactions = async () => {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) throw new Error("❌ ไม่พบ Token กรุณาเข้าสู่ระบบใหม่");

        const response = await axios.get("/api/transactions", {
            headers: { Authorization: `Bearer ${token}` },
        });

        console.log("✅ โหลดธุรกรรมสำเร็จ:", response.data);
        return response.data;
    } catch (error: any) {
        console.error("❌ โหลดธุรกรรมล้มเหลว:", error.response?.data || error.message);
        throw error;
    }
};

export const createTransaction = async (transactionData: any) => {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) throw new Error("❌ ไม่พบ Token กรุณาเข้าสู่ระบบใหม่");

        const response = await axios.post("/api/transactions", transactionData, {
            headers: { Authorization: `Bearer ${token}` },
        });

        console.log("✅ บันทึกธุรกรรมสำเร็จ", response.data);
        return response.data;
    } catch (error: any) {
        console.error("❌ บันทึกธุรกรรมล้มเหลว:", error.response?.data || error.message);
        throw error;
    }
};
