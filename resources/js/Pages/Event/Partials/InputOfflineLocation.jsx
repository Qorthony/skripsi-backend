import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";

export default function InputOfflineLocation({data, setData, errors}) {
    return (
        <>
            <div>
                <InputLabel htmlFor="kota" value="Kota" />
                <TextInput 
                    id="kota" 
                    type="text" 
                    className="mt-1 block w-full" 
                    value={data.kota} 
                    onChange={(e) => setData('kota', e.target.value)}
                    autoComplete="kota"
                    required
                />
                <InputError message={errors.kota} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="alamat_lengkap" value="Alamat Lengkap" />
                <TextInput 
                    id="alamat_lengkap" 
                    type="text" 
                    className="mt-1 block w-full" 
                    value={data.alamat_lengkap} 
                    onChange={(e) => setData('alamat_lengkap', e.target.value)}
                    autoComplete="alamat_lengkap"
                    required
                /> 
                <InputError message={errors.alamat_lengkap} className="mt-2" />
            </div>
        </>
    );
}