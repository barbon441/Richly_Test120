interface CategorySelectProps {
    categories: { id: number; name: string; icon: string }[];
    selected: number | null;
    onSelect: (id: number) => void;
}

const CategorySelect: React.FC<CategorySelectProps> = ({ categories, selected, onSelect }) => {
    if (!Array.isArray(categories) || categories.length === 0) {
        console.warn("⚠️ ไม่มีหมวดหมู่ หรือ API ส่งข้อมูลผิดพลาด:", categories);
        return <p className="text-red-500 text-center mt-4 font-semibold">⚠️ ไม่พบหมวดหมู่ กรุณาลองใหม่</p>;
    }

    return (
        <div className="bg-white p-5 rounded-lg shadow-md mx-4 mt-4 border border-gray-300 w-full max-w-2xl">
            <h3 className="text-lg font-semibold text-gray-700 mb-4 text-center">เลือกหมวดหมู่</h3>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                {categories.map((cat) => (
                    <button
                    key={cat.id}
                    onClick={() => onSelect(cat.id)}
                    className={`p-5 rounded-xl shadow-lg text-center transition-all duration-300 flex flex-col items-center ${
                        selected === cat.id ? "bg-amber-500 text-white scale-105 shadow-xl" : "bg-gray-100 text-gray-700 hover:bg-amber-200"
                    }`}
                >
                    <span className="text-3xl">{cat.icon}</span>
                    <p className="text-sm mt-1 font-semibold">{cat.name}</p>
                </button>
                ))}
            </div>
        </div>
    );
};

export default CategorySelect;
