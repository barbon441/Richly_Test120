import axios from "axios";

export const getCategories = async (type: "income" | "expense") => {
    try {
        const response = await axios.get(`/api/categories?type=${type}`);
        console.log("🔍 API Response:", response.data); // ✅ ตรวจสอบค่าที่ API ส่งมา

        if (!Array.isArray(response.data)) {
            console.error("❌ API ส่งค่าผิดพลาด categories =", response.data);
            return [];
        }

        return response.data;
    } catch (error: any) {
        console.error("❌ โหลดหมวดหมู่ล้มเหลว:", error.response?.data || error.message);
        return [];
    }
};
