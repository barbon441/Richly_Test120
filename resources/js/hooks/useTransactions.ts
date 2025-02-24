import { useEffect, useState } from "react";
import { getTransactions } from "@/services/transactionService";

export const useTransactions = () => {
    const [transactions, setTransactions] = useState([]);

    useEffect(() => {
        getTransactions()
            .then(setTransactions)
            .catch(console.error);
    }, []);

    return transactions;
};
