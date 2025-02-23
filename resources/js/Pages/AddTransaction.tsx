import { router } from "@inertiajs/react";
import axios from "axios";
import { useEffect, useState } from "react";

const expenseCategories = [
  { id: 1, name: "อาหาร", icon: "🍔" },
  { id: 2, name: "การเดินทาง", icon: "🚗" },
  { id: 3, name: "ที่อยู่อาศัย", icon: "🏠" },
  { id: 4, name: "ของใช้", icon: "🛒" },
  { id: 5, name: "อื่นๆ", icon: "🛠️" },
];

const incomeCategories = [
    { id: 6, name: "เงินเดือน", icon: "💵" },
    { id: 7, name: "โบนัส", icon: "🎉" },
    { id: 8, name: "ธุรกิจ", icon: "🏢" },
    { id: 9, name: "ครอบครัว", icon: "👨‍👩‍👧‍👦" },
    { id: 10, name: "อื่นๆ", icon: "🛠️" },
  ];

const AddTransaction = () => {
  const params = new URLSearchParams(window.location.search);
  const transactionId = params.get("id"); // ดึง id จาก URL

  // ตั้งค่า state ให้เป็น string เสมอ
  const [amount, setAmount] = useState("");
  const [note, setNote] = useState("");
  const [transactionType, setTransactionType] = useState<"expense" | "income">("expense");
  const [category, setCategory] = useState(expenseCategories[0].id);

  const categories = transactionType === "expense" ? expenseCategories : incomeCategories;

  // โหลดข้อมูลธุรกรรม (สำหรับแก้ไข)
  useEffect(() => {
    if (transactionId) {
        axios.get(`/transactions/${transactionId}`)
            .then(response => {
                const data = response.data;
                setAmount(data.amount.toString());
                setNote(data.description || "");
                setTransactionType(data.transaction_type);
                setCategory(data.category_id);
            })
            .catch(error => console.error("❌ โหลดข้อมูลไม่สำเร็จ", error));
    }
}, [transactionId]);

  // เมื่อ transactionType เปลี่ยน ตรวจสอบว่า category ที่เลือกอยู่ในรายการของประเภทใหม่หรือไม่
  useEffect(() => {
    const newCategoryList = transactionType === "expense" ? expenseCategories : incomeCategories;
    // ถ้าค่า category ที่เลือกไม่อยู่ในรายการใหม่ ให้รีเซ็ตเป็นค่าแรก
    if (!newCategoryList.some((cat) => cat.id === category)) {
      setCategory(newCategoryList[0].id);
    }
  }, [transactionType]);

  // คำนวณตัวเลขจากคีย์แพด
  const handleKeyPress = (key: string) => {
    if (amount === "Error") setAmount("");

    if (key === "=") {
      try {
        const result = eval(amount);
        if (!isNaN(result)) {
          setAmount(result.toString());
        } else {
          setAmount("Error");
        }
      } catch {
        setAmount("Error");
      }
    } else {
      setAmount((prev) => prev + key);
    }
  };

  // ลบตัวเลขจากช่องป้อนข้อมูล
  const handleDelete = () => {
    setAmount((prev) => prev.slice(0, -1));
  };
const [loading, setLoading] = useState(false); // ✅ เพิ่ม State สำหรับ Loader

// ✅ ส่งข้อมูลธุรกรรม (POST หรือ PUT)
const handleSubmit = async () => {
    if (!amount) {
        console.error("❌ กรุณากรอกจำนวนเงินที่ถูกต้อง");
        return;
    }

    setLoading(true); // ⏳ เปิด Loader ขณะบันทึก

    const finalAmount =
        transactionType === "expense"
            ? `-${Math.abs(Number(amount))}`
            : `${Math.abs(Number(amount))}`;

    const transaction_date = new Date().toISOString().split("T")[0];

    if (!category) {
        console.error("❌ กรุณาเลือกหมวดหมู่");
        return;
    }

    // ✅ ดึง Token จาก Local Storage หรือ Context
    const token = localStorage.getItem("auth_token");
    console.log("🔎 Token ที่ใช้:", token);

    if (!token) {
        console.error("❌ ไม่พบ Token กรุณาเข้าสู่ระบบใหม่");
        return;
    }

    const headers = {
    "Content-Type": "application/json",
    Accept: "application/json",
    Authorization: `Bearer ${token}`, // ✅ ตรวจสอบว่า Token ถูกส่งไป
    };




    const transactionData = {
        category_id: category,
        amount: finalAmount,
        transaction_type: transactionType,
        description: note,
        transaction_date,
    };

    console.log("📤 กำลังส่งข้อมูลไปยังเซิร์ฟเวอร์:", transactionData);

    try {
        let response;
        if (transactionId) {
            response = await axios.put(`/api/transactions/${transactionId}`, transactionData, { headers });
        } else {
            response = await axios.post("/api/transactions", transactionData, { headers });
        }

        console.log("✅ Response จากเซิร์ฟเวอร์:", response.data);

        if (response.status >= 200 && response.status < 300) {
            console.log("✅ ธุรกรรมถูกบันทึกเรียบร้อย!");

            window.dispatchEvent(new Event("transactionAdded")); // ✅ แจ้งให้ Dashboard โหลดข้อมูลใหม่

            console.log("🔄 กำลังเปลี่ยนหน้าไปยัง Dashboard...");
            window.location.href = "/dashboard"; // ✅ ใช้ window.location แทน router.visit()
        } else {
            console.error("❌ บันทึกข้อมูลล้มเหลว:", response.status);
        }
    } catch (error: any) {
        console.error("❌ Error ในการบันทึก:", error.response?.data || error.message);
    } finally {
        setLoading(false); // 🔄 ปิด Loader
    }
};


// ✅ ปุ่มบันทึกที่มี Loader
<button
    onClick={handleSubmit}
    disabled={loading} // ❌ ปิดปุ่มถ้า API กำลังทำงาน
    className="p-4 rounded-lg text-2xl font-semibold bg-green-500 hover:bg-green-600 text-white"
>
    {loading ? "⏳ กำลังบันทึก..." : "✅ บันทึก"}
</button>


  return (
    <div className="min-h-screen bg-amber-50">
      <div className="bg-amber-400 text-white p-4 flex justify-between items-center shadow-md">
        <button onClick={() => history.back()} className="text-xl">
          🔙
        </button>
        <h2 className="text-lg font-semibold">
          {transactionType === "expense" ? "เพิ่มรายจ่าย" : "เพิ่มรายรับ"}
        </h2>
      </div>

      {/* ปุ่มเลือก รายจ่าย/รายรับ */}
      <div className="flex justify-center mt-4">
        <button
          onClick={() => setTransactionType("income")}
          className={`px-4 py-2 mx-2 rounded-lg shadow-md text-lg font-semibold ${
            transactionType === "income" ? "bg-green-400 text-white" : "bg-gray-200 text-gray-700"
          }`}
        >
          รายรับ 💰
        </button>
        <button
          onClick={() => setTransactionType("expense")}
          className={`px-4 py-2 mx-2 rounded-lg shadow-md text-lg font-semibold ${
            transactionType === "expense" ? "bg-red-400 text-white" : "bg-gray-200 text-gray-700"
          }`}
        >
          รายจ่าย 💸
        </button>
      </div>

      {/* เลือกหมวดหมู่ */}
      <div className="bg-white p-4 rounded-lg shadow-lg mx-4 mt-4">
        <h3 className="text-lg font-semibold text-gray-700 mb-2">เลือกหมวดหมู่</h3>
        <div className="grid grid-cols-4 gap-4">
          {categories.map((cat) => (
            <button
              key={cat.id}
              onClick={() => {
                setCategory(cat.id);
                console.log("🟢 หมวดหมู่ที่เลือกใหม่:", cat.id);
              }}
              className={`p-3 rounded-lg shadow-md text-center ${
                category === cat.id ? "bg-amber-400 text-white" : "bg-gray-100 text-gray-700 hover:bg-amber-100"
              }`}
            >
              <span className="text-2xl">{cat.icon}</span>
              <p className="text-sm mt-1">{cat.name}</p>
            </button>
          ))}
        </div>
      </div>

      {/* รายละเอียดธุรกรรม */}
      <div className="bg-white p-4 rounded-lg shadow-lg mx-4 mt-4">
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

      <div className="bg-amber-200 text-black p-6 mt-6 rounded-t-lg shadow-lg">
        <div className="grid grid-cols-4 gap-3 mt-4">
          {[
            "7",
            "8",
            "9",
            "+",
            "4",
            "5",
            "6",
            "-",
            "1",
            "2",
            "3",
            "*",
            ".",
            "0",
            "=",
            "/",
          ].map((key) => (
            <button
              key={key}
              onClick={() => handleKeyPress(key)}
              className="p-4 rounded-lg text-2xl font-semibold bg-amber-100 hover:bg-amber-400"
            >
              {key}
            </button>
          ))}
        </div>
        <div className="grid grid-cols-2 gap-3 mt-3">
          <button
            onClick={handleDelete}
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
    </div>
  );
};

export default AddTransaction;
