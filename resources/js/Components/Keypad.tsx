interface KeypadProps {
    onPress: (key: string) => void;
    onDelete: () => void;
}

const Keypad: React.FC<KeypadProps> = ({ onPress, onDelete }) => {
    return (
        <div className="grid grid-cols-4 gap-3 p-6 bg-gray-100 rounded-lg w-full max-w-6xl mx-auto">
            {["7", "8", "9", "4", "5", "6", "1", "2", "3", "0", ".", "="].map((key) => (
                <button key={key} onClick={() => onPress(key)} className="p-4 bg-white rounded-lg text-xl shadow-md">
                    {key}
                </button>
            ))}
        </div>
    );
};

export default Keypad;
