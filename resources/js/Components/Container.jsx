export default function Container({ children, withoutBg }) {
    return (
        <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div className={`overflow-hidden ${withoutBg?"":"bg-white"} shadow-sm sm:rounded-lg`}>
                <div className="p-6">
                    {children}
                </div>
            </div> 
        </div>
    );
}