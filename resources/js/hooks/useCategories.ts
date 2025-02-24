import { useEffect, useState } from "react";
import { getCategories } from "@/services/categoryService";

const DEFAULT_CATEGORIES = [
    { id: 1, name: "อาหาร", icon: "🍽️" },
    { id: 2, name: "เดินทาง", icon: "🚖" },
    { id: 3, name: "ช้อปปิ้ง", icon: "🛍️" }
];

export const useCategories = (type: "income" | "expense") => {
    const [categories, setCategories] = useState(DEFAULT_CATEGORIES);

    useEffect(() => {
        const fetchCategories = async () => {
            try {
                const data = await getCategories(type);
                setCategories(data.length > 0 ? data : DEFAULT_CATEGORIES);
            } catch (error) {
                console.error("❌ โหลดหมวดหมู่ล้มเหลว:", error);
                setCategories(DEFAULT_CATEGORIES);
            }
        };
        fetchCategories();
    }, [type]);

    return categories;
};

