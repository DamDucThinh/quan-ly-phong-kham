# Nội dung phần thân báo cáo (Chương 1–4, Phụ lục) theo phương án 4 vai trò, không có kho thuốc.
# Được dung-bao-cao.py nạp bằng exec, dùng các biến T, p, gach, u1, u2, u3, HINH, ANH của file đó.

# =====================================================================================
# CHƯƠNG 1
# =====================================================================================
u1('Mô tả hệ thống và BFD')
u2('Khảo sát nghiệp vụ thực tế')
u3('Phương pháp khảo sát')
p('Để nắm được cách một phòng khám nhỏ đang vận hành, việc khảo sát được tiến hành theo ba cách bổ trợ cho nhau:')
gach('**Phỏng vấn** người quản trị phòng khám, nhân viên quầy tiếp đón và các bác sĩ về quy trình hẹn lịch, tiếp nhận, khám '
     'bệnh, kê đơn và tổng hợp số liệu, theo bộ câu hỏi ở Phụ lục B.')
gach('**Quan sát** trực tiếp công việc tại quầy tiếp đón và phòng khám trong giờ khám: cách ghi sổ hẹn, nhận lịch qua điện thoại, '
     'gọi bệnh nhân vào khám, viết và lưu phiếu khám, đơn thuốc.')
gach('**Phiếu khảo sát** dành cho bệnh nhân về cách đặt lịch hiện nay, thời gian chờ và mong muốn đối với việc đặt lịch '
     'trực tuyến, theo mẫu ở Phụ lục C.')
p('Kế hoạch khảo sát (đối tượng, nội dung, thời gian) được trình bày ở Phụ lục A. Các giấy tờ thu thập được trong nghiệp vụ '
  'gồm sổ hẹn lịch, phiếu kết quả khám, đơn thuốc và bảng tổng hợp cuối tháng.')
u3('Giới thiệu phòng khám')
p('NichegHopital là phòng khám đa khoa quy mô nhỏ với 5 bác sĩ thuộc 5 chuyên khoa: Nội tổng quát, Nhi khoa, Tai Mũi Họng, '
  'Da liễu và Răng Hàm Mặt, mỗi bác sĩ một phòng khám riêng. Bác sĩ làm việc theo các ca cố định trong tuần (ca sáng '
  '07:30–11:30, ca chiều 13:30–17:00), Chủ nhật nghỉ. Ngoài các bác sĩ, phòng khám có nhân viên ở quầy tiếp đón nhận lịch hẹn và '
  'tiếp nhận bệnh nhân, và người quản trị phụ trách thông tin bác sĩ, lịch làm việc, danh mục thuốc, tài khoản người dùng và theo '
  'dõi báo cáo. Phòng khám không có quầy thuốc: bác sĩ kê đơn, bệnh nhân mang đơn ra nhà thuốc bên ngoài để mua.')
u3('Hiện trạng và vấn đề')
p('Phần lớn công việc hiện được làm bằng sổ sách và bảng tính, dẫn tới các vấn đề sau:')
gach('Bệnh nhân phải gọi điện hoặc đến quầy để hẹn lịch; nhân viên ghi tay vào sổ nên có lúc hai người cùng được hẹn một khung '
     'giờ của một bác sĩ.')
gach('Bác sĩ phải hỏi quầy tiếp đón hoặc xem sổ mới biết hôm nay có những bệnh nhân nào đang chờ mình.')
gach('Kết quả khám và đơn thuốc lưu trên giấy, khi bệnh nhân tái khám bác sĩ khó tra lại các lần khám trước.')
gach('Đơn thuốc viết tay dễ sai tên thuốc, ghi trùng một thuốc, hoặc khó đọc khi bệnh nhân mang ra nhà thuốc.')
gach('Cuối tháng, nhân viên phải đếm tay số lượt khám và lượng thuốc đã kê để báo cáo.')
u3('Quy trình nghiệp vụ')
gach('**Đặt lịch:** bệnh nhân tự đăng ký khám trên web, hoặc gọi điện, đến quầy để nhân viên sắp xếp lịch hộ: chọn bác sĩ, ngày '
     'khám trong 7 ngày tới, một khung giờ còn trống và ghi lý do khám. Lịch có thể đổi hoặc hủy trước giờ khám.', 21)
gach('**Tiếp nhận:** đến ngày hẹn, bệnh nhân tới quầy. Nhân viên tra cứu lịch hẹn, kiểm tra và bổ sung hồ sơ bệnh nhân rồi lập '
     'phiếu tiếp nhận để đưa bệnh nhân vào hàng chờ của bác sĩ. Người đến khám không hẹn trước được tạo hồ sơ và xếp vào hàng chờ '
     'của bác sĩ được chọn.', 21)
gach('**Khám bệnh và kê đơn:** bác sĩ tra cứu lịch khám, gọi bệnh nhân trong hàng chờ của mình vào khám, xem hồ sơ các lần khám '
     'trước, ghi chỉ số sinh tồn, kết quả khám, chẩn đoán, lời dặn và kê đơn từ danh mục thuốc. Phiếu kết quả khám và đơn thuốc '
     'được in cho bệnh nhân; bệnh nhân mua thuốc theo đơn ở nhà thuốc bên ngoài.', 21)
gach('**Quản trị và báo cáo:** quản trị cập nhật thông tin bác sĩ, lịch làm việc, danh mục thuốc và tài khoản người dùng; định '
     'kỳ xem báo cáo lượt khám và thuốc đã kê.', 21)
u3('Người dùng của hệ thống')
p('Hệ thống có bốn vai trò người dùng: Bệnh nhân, Nhân viên, Bác sĩ và Quản trị. Mỗi người chỉ thấy các chức năng của vai trò '
  'mình; tài khoản nhân viên, bác sĩ và quản trị do quản trị cấp, bệnh nhân tự đăng ký bằng số điện thoại.')
T.bang_du_lieu('Người dùng và công việc trên hệ thống', ['Người dùng', 'Công việc trên hệ thống'], [
    ['Bệnh nhân', 'Đăng ký tài khoản bằng số điện thoại, đăng nhập; đăng ký khám chữa bệnh (đặt, đổi, hủy lịch hẹn); xem lịch '
                  'khám, thông báo, kết quả khám và đơn thuốc; tải phiếu dạng PDF.'],
    ['Nhân viên', 'Sắp xếp lịch khám hộ bệnh nhân gọi điện hoặc đến quầy (đặt, đổi, hủy lịch), thêm hồ sơ bệnh nhân mới; tra '
                  'cứu, tiếp nhận bệnh nhân có hẹn và không hẹn trước, lập phiếu tiếp nhận; xem danh sách lịch hẹn, in phiếu.'],
    ['Bác sĩ', 'Tra cứu lịch khám: hàng chờ khám và lịch làm việc của mình; gọi bệnh nhân vào khám; xem hồ sơ bệnh án các lần '
               'trước; ghi kết quả khám, chẩn đoán, kê đơn thuốc; in phiếu.'],
    ['Quản trị', 'Quản lý thông tin bác sĩ, lịch làm việc của bác sĩ và danh mục thuốc; tạo, sửa, khóa tài khoản người dùng; theo '
                 'dõi tình hình trong ngày; xem báo cáo lượt khám, thuốc đã kê.'],
], [2155, 6917])
u3('Quy tắc nghiệp vụ')
gach('**Khung giờ khám:** thời gian khám chia thành các khung 30 phút cắt từ ca làm việc của bác sĩ; mỗi khung chỉ nhận một bệnh '
     'nhân. Lịch đặt được trong 7 ngày tính cả hôm nay, không đặt giờ đã qua.')
gach('**Không đặt trùng:** khung giờ đã có người giữ chỗ không cho người khác chọn; khi xác nhận, hệ thống kiểm tra lại khung giờ. '
     'Lịch bị hủy thì khung giờ được trả lại.')
gach('**Đặt lịch hộ:** nhân viên sắp xếp lịch qua cùng các bước chọn bác sĩ, chọn giờ, xác nhận như bệnh nhân; lịch được ghi là do '
     'nhân viên đặt hộ. Người gọi điện chưa có hồ sơ được nhân viên thêm mới bằng họ tên và số điện thoại.')
gach('**Đổi, hủy lịch:** chỉ áp dụng cho lịch chưa được tiếp nhận và chưa tới giờ khám; khi hủy chọn lý do hủy.')
gach('**Khám bệnh:** bác sĩ chỉ xem và khám các bệnh nhân trong hàng chờ của mình.')
gach('**Kê đơn:** thuốc phải có trong danh mục, không trùng trong cùng một đơn, số lượng là số nguyên lớn hơn 0. Mỗi lần khám có '
     'tối đa một đơn thuốc. Phòng khám không bán thuốc nên không quản lý tồn kho; đơn được in để bệnh nhân mua thuốc ở ngoài.')
gach('**Tài khoản:** bệnh nhân đăng nhập bằng số điện thoại; nếu số đó đã có hồ sơ do phòng khám tạo thì tài khoản được gắn vào hồ '
     'sơ đó. Tài khoản nhân viên, bác sĩ, quản trị do quản trị tạo, tài khoản bác sĩ gắn với một hồ sơ bác sĩ; tài khoản bị khóa '
     'không đăng nhập được.')
gach('**Thông báo:** mỗi lần đặt, đổi, hủy lịch, có kết quả khám hoặc đơn thuốc, bệnh nhân nhận một thông báo trên trang của mình.')
p('Lịch hẹn đi qua các trạng thái trong bảng sau.')
T.bang_du_lieu('Các trạng thái của lịch hẹn', ['Trạng thái', 'Khi nào chuyển sang trạng thái này'], [
    ['Chưa đến', 'Bệnh nhân hoặc nhân viên vừa đặt lịch thành công'],
    ['Chờ khám', 'Nhân viên tiếp nhận bệnh nhân, lập phiếu tiếp nhận'],
    ['Đang khám', 'Bác sĩ gọi bệnh nhân vào khám'],
    ['Đã khám', 'Bác sĩ lưu bệnh án của lần khám'],
    ['Đã hủy', 'Lịch bị hủy trước giờ khám; khung giờ được trả lại'],
], [2400, 6672])
u3('Yêu cầu đối với hệ thống')
p('Từ kết quả khảo sát, hệ thống cần đáp ứng các yêu cầu sau:')
gach('**Chức năng:** quản trị hệ thống (thông tin bác sĩ, lịch làm việc, danh mục thuốc, tài khoản người dùng); quản lý lịch hẹn '
     '(đặt, kiểm tra lịch trống, xác nhận, đổi, hủy, kể cả đặt hộ); quản lý hồ sơ bệnh án (tiếp nhận, tra cứu lịch khám và lịch sử '
     'khám, cập nhật bệnh án); kê đơn thuốc (lập, kiểm tra, lưu và in); báo cáo thống kê (lượt khám, thuốc đã kê).')
gach('**Phân quyền:** bốn vai trò, mỗi vai trò chỉ thấy chức năng và dữ liệu của mình; quản trị khóa được tài khoản người dùng.')
gach('**Dễ dùng:** giao diện tiếng Việt, mỗi màn hình làm một việc, thông báo lỗi nói rõ cần sửa gì.')
gach('**Toàn vẹn dữ liệu:** không có hai bệnh nhân giữ cùng một khung giờ của một bác sĩ; bệnh án và đơn thuốc được lưu cùng lúc '
     'trong một giao dịch.')
gach('**An toàn:** mật khẩu được mã hóa; tài khoản bị khóa không đăng nhập được.')
u2('Sơ đồ phân rã chức năng (BFD)')
T.hinh_ngang('Sơ đồ phân rã chức năng biểu diễn các chức năng của hệ thống dưới dạng cây: nút gốc là tên hệ thống, mỗi nút được '
             'chia nhỏ thành các chức năng con cho tới khi đủ đơn giản để mô tả bằng một quy trình xử lý. Hệ thống quản lý phòng '
             'khám gồm 5 chức năng chính với 16 chức năng con.',
             HINH + '01-BFD-so-do-phan-ra-chuc-nang.png', 'Sơ đồ phân rã chức năng (BFD) của hệ thống', rong=25)
p('Nội dung từng chức năng con được mô tả trong bảng sau.')
T.bang_du_lieu('Mô tả các chức năng của hệ thống', ['Chức năng chính', 'Chức năng con', 'Mô tả'], [
    ['1. Quản trị hệ thống', '1.1 Cập nhật thông tin bác sĩ', 'Quản trị thêm bác sĩ mới, sửa họ tên, chuyên khoa, phòng, số điện thoại, email, trình độ.'],
    ['', '1.2 Cập nhật lịch làm việc bác sĩ', 'Quản trị khai báo các ca làm việc trong tuần của từng bác sĩ; bác sĩ xem lịch làm việc của mình.'],
    ['', '1.3 Cập nhật danh mục thuốc', 'Quản trị thêm thuốc (mã tự sinh), sửa tên, hàm lượng, đơn vị, nhóm, liều tối đa, lưu ý.'],
    ['', '1.4 Quản lý tài khoản người dùng', 'Quản trị tạo, sửa, khóa, mở khóa tài khoản nhân viên, bác sĩ, quản trị; bệnh nhân tự đăng ký; mọi người dùng đăng nhập.'],
    ['2. Quản lý lịch hẹn', '2.1 Tiếp nhận yêu cầu đặt lịch', 'Nhận yêu cầu đăng ký khám của bệnh nhân hoặc yêu cầu sắp xếp lịch của nhân viên đặt hộ (tìm hoặc thêm bệnh nhân); chọn bác sĩ, ngày khám; phân loại đặt mới hay đổi, hủy.'],
    ['', '2.2 Kiểm tra lịch trống', 'Tìm các khung giờ còn trống của bác sĩ trong ngày từ lịch làm việc và lịch đã đặt.'],
    ['', '2.3 Xác nhận lịch hẹn', 'Kiểm tra lại khung giờ, ghi lịch hẹn kèm người đặt, gửi lịch khám cho bệnh nhân.'],
    ['', '2.4 Đổi, hủy lịch hẹn', 'Đổi sang khung giờ khác hoặc hủy lịch chưa tới giờ khám, trả lại khung giờ.'],
    ['3. Quản lý hồ sơ bệnh án', '3.1 Tiếp nhận bệnh nhân', 'Nhân viên tra cứu lịch hẹn trong ngày, cập nhật hoặc tạo hồ sơ bệnh nhân, lập phiếu tiếp nhận, đưa vào hàng chờ của bác sĩ.'],
    ['', '3.2 Tra cứu lịch khám, lịch sử khám', 'Bác sĩ xem hàng chờ khám của mình và các lần khám trước của bệnh nhân: chẩn đoán, chỉ số, kết quả, đơn thuốc.'],
    ['', '3.3 Cập nhật bệnh án', 'Bác sĩ ghi chỉ số sinh tồn, kết quả khám, chẩn đoán, lời dặn; gửi kết quả khám cho bệnh nhân.'],
    ['4. Kê đơn thuốc', '4.1 Lập đơn thuốc', 'Bác sĩ chọn thuốc trong danh mục, nhập số lượng, cách dùng.'],
    ['', '4.2 Kiểm tra thuốc', 'Đối chiếu với danh mục, phát hiện thuốc trùng trong đơn, số lượng sai.'],
    ['', '4.3 Lưu và in đơn thuốc', 'Lưu đơn cùng bệnh án, in đơn cho bệnh nhân mang ra nhà thuốc mua, gửi đơn vào tài khoản bệnh nhân.'],
    ['5. Báo cáo thống kê', '5.1 Thống kê lượt khám', 'Số lượt khám theo ngày, theo bác sĩ trong một khoảng thời gian.'],
    ['', '5.2 Thống kê thuốc đã kê', 'Số đơn và số lượng từng loại thuốc đã kê trong một khoảng thời gian.'],
], [2200, 2800, 4072], gop_cot_dau=True)

# =====================================================================================
# CHƯƠNG 2
# =====================================================================================
u1('Mô hình hóa tiến trình (DFD)')
p('Sơ đồ luồng dữ liệu mô tả dữ liệu di chuyển giữa các tác nhân ngoài, các tiến trình xử lý và các kho dữ liệu. Sơ đồ được vẽ '
  'theo ba mức: mức ngữ cảnh coi cả hệ thống là một tiến trình; mức 1 tách thành 5 chức năng chính của BFD; mức 2 chi tiết từng '
  'chức năng. Luồng dữ liệu vào, ra ở mức dưới khớp với mức trên. Bộ ký hiệu sử dụng như sau.')
T.bang_du_lieu('Ký hiệu dùng trong sơ đồ luồng dữ liệu', ['Thành phần', 'Ký hiệu', 'Ý nghĩa'], [
    ['Tác nhân ngoài', 'Hình chữ nhật', 'Người hoặc bộ phận bên ngoài hệ thống, gửi dữ liệu vào hoặc nhận thông tin ra.'],
    ['Tiến trình', 'Hình tròn có số hiệu', 'Một chức năng xử lý, biến đổi dữ liệu vào thành dữ liệu ra.'],
    ['Luồng dữ liệu', 'Mũi tên có nhãn', 'Dữ liệu di chuyển giữa các thành phần; nhãn là tên dữ liệu.'],
    ['Kho dữ liệu', 'Hai đường kẻ song song', 'Nơi lưu dữ liệu để dùng lại; mũi tên hai chiều là vừa đọc vừa ghi.'],
], [2000, 2400, 4672])
p('Hệ thống có bốn tác nhân ngoài là **Bệnh nhân**, **Nhân viên**, **Bác sĩ** và **Quản trị**, cùng bảy kho dữ liệu: D1 Hồ sơ bác '
  'sĩ, D2 Hồ sơ bệnh nhân, D3 Danh mục thuốc, D4 Lịch hẹn, D5 Hồ sơ bệnh án, D6 Đơn thuốc, D7 Tài khoản người dùng.')
u2('Sơ đồ luồng dữ liệu mức ngữ cảnh')
p('Ở mức ngữ cảnh, toàn bộ hệ thống là tiến trình 0, bốn tác nhân đặt ở bốn góc. Bệnh nhân gửi yêu cầu đăng ký, đăng nhập và đăng '
  'ký khám chữa bệnh; nhận lại lịch khám chữa bệnh và kết quả khám chữa bệnh (gồm phiếu kết quả khám và đơn thuốc). Bác sĩ gửi yêu '
  'cầu tra cứu lịch khám chữa bệnh và kết quả khám chữa bệnh (kết quả khám, chẩn đoán, chỉ định thuốc); nhận lại thông tin lịch khám '
  'chữa bệnh (hàng chờ, hồ sơ bệnh án, lịch làm việc). Nhân viên gửi yêu cầu tra cứu, tiếp nhận bệnh nhân và sắp xếp lịch khám chữa '
  'bệnh cho người gọi điện, đến quầy; nhận danh sách lịch khám chữa bệnh. Quản trị quản lý tài khoản người dùng, thông tin bác sĩ, '
  'lịch làm việc của bác sĩ và gửi yêu cầu báo cáo; nhận lại báo cáo thống kê. Phòng khám không bán thuốc nên sơ đồ không có luồng '
  'nào về kho thuốc; đơn thuốc là một phần của kết quả khám chữa bệnh.')
T.hinh_anh(HINH + '02-DFD-muc-0-khung-canh.png', 'DFD mức ngữ cảnh', rong=16)
u2('Sơ đồ luồng dữ liệu mức 1')
T.hinh_ngang('DFD mức 1 tách tiến trình 0 thành 5 tiến trình ứng với 5 chức năng chính. Các luồng với tác nhân giữ đúng tên ở mức '
             'ngữ cảnh, phần trong ngoặc cho biết luồng mang phần dữ liệu nào. Tiến trình 1 ghi hồ sơ bác sĩ (D1), danh mục thuốc (D3) '
             'và tài khoản người dùng (D7), trả lịch làm việc cho bác sĩ. Tiến trình 2 đọc lịch làm việc ở D1 và lịch đã đặt ở D4 để '
             'tìm khung giờ trống, ghi lịch hẹn vào D4 và thêm bệnh nhân mới vào D2. Tiến trình 3 đọc lịch hẹn trong ngày, cập nhật hồ '
             'sơ bệnh nhân (D2), trạng thái lịch hẹn (D4) và bệnh án (D5). Tiến trình 4 lấy chẩn đoán từ D5, danh mục thuốc từ D3 rồi '
             'ghi đơn thuốc vào D6. Tiến trình 5 tổng hợp lượt khám từ D5, thuốc đã kê từ D6 và tên thuốc từ D3.',
             HINH + '03-DFD-muc-1-dinh.png', 'DFD mức 1', rong=25, cao=15.5)
u2('Sơ đồ luồng dữ liệu mức 2')
u3('Chức năng 1: Quản trị hệ thống')
p('Quản trị cập nhật thông tin bác sĩ (1.1) và lịch làm việc (1.2), cả hai ghi vào D1; bác sĩ nhận lại thông tin lịch khám chữa bệnh '
  'là lịch làm việc của mình. Quản trị cập nhật danh mục thuốc (1.3) trong D3. Tiến trình 1.4 ghi tài khoản người dùng vào D7: quản '
  'trị tạo tài khoản (tài khoản bác sĩ gắn với một bác sĩ chưa có tài khoản trong D1), bệnh nhân tự đăng ký và đăng nhập.')
T.hinh_anh(HINH + '04-DFD-muc-2-chuc-nang-1-quan-tri-he-thong.png', 'DFD mức 2 – Chức năng 1: Quản trị hệ thống', rong=16)
u3('Chức năng 2: Quản lý lịch hẹn')
p('Yêu cầu đăng ký khám chữa bệnh của bệnh nhân, hoặc yêu cầu sắp xếp lịch của nhân viên cho người gọi điện, đến quầy, được tiếp '
  'nhận ở 2.1; nhân viên tìm hồ sơ bệnh nhân trong D2 hoặc thêm bệnh nhân mới. Yêu cầu đặt mới sang 2.2 để kiểm tra lịch trống dựa '
  'trên lịch làm việc (D1) và lịch đã đặt (D4); khung giờ trống được xác nhận ở 2.3, ghi vào D4, gửi lịch khám chữa bệnh cho bệnh '
  'nhân và đưa vào danh sách lịch khám chữa bệnh của nhân viên. Yêu cầu đổi, hủy đi vào 2.4; khi đổi lịch, 2.4 chuyển yêu cầu chọn '
  'giờ mới về 2.2.')
T.hinh_anh(HINH + '05-DFD-muc-2-chuc-nang-2-quan-ly-lich-hen.png', 'DFD mức 2 – Chức năng 2: Quản lý lịch hẹn', rong=16)
u3('Chức năng 3: Quản lý hồ sơ bệnh án')
p('Tiến trình 3.1 do nhân viên thực hiện: tra cứu, tiếp nhận bệnh nhân theo lịch hẹn trong ngày (D4), tìm hoặc thêm hồ sơ trong D2, '
  'lập phiếu tiếp nhận và chuyển lịch sang Chờ khám. Khi bác sĩ tra cứu lịch khám chữa bệnh, tiến trình 3.2 lấy hàng chờ khám từ D4 '
  'và lịch sử khám từ D5 để trả về. Bác sĩ gửi kết quả khám chữa bệnh vào 3.3; tiến trình này ghi bệnh án mới vào D5, chuyển lịch '
  'sang Đã khám và gửi kết quả khám chữa bệnh cho bệnh nhân.')
T.hinh_anh(HINH + '06-DFD-muc-2-chuc-nang-3-quan-ly-ho-so-benh-an.png', 'DFD mức 2 – Chức năng 3: Quản lý hồ sơ bệnh án', rong=16)
u3('Chức năng 4: Kê đơn thuốc')
p('Tiến trình 4.1 lập đơn từ chỉ định thuốc trong kết quả khám của bác sĩ và chẩn đoán trong D5. Tiến trình 4.2 đối chiếu từng thuốc '
  'với danh mục thuốc trong D3; thuốc trùng, số lượng sai được trả về 4.1 để điều chỉnh. Đơn hợp lệ được 4.3 lưu vào D6 và in cho '
  'bệnh nhân mang ra nhà thuốc bên ngoài mua.')
T.hinh_anh(HINH + '07-DFD-muc-2-chuc-nang-4-ke-don-thuoc.png', 'DFD mức 2 – Chức năng 4: Kê đơn thuốc', rong=16)
u3('Chức năng 5: Báo cáo thống kê')
p('Theo yêu cầu báo cáo của Quản trị, tiến trình 5.1 thống kê lượt khám từ D5, tiến trình 5.2 thống kê thuốc đã kê từ đơn thuốc (D6) '
  'và danh mục thuốc (D3).')
T.hinh_anh(HINH + '08-DFD-muc-2-chuc-nang-5-bao-cao-thong-ke.png', 'DFD mức 2 – Chức năng 5: Báo cáo thống kê', rong=16)
u2('Từ điển dữ liệu')
p('Từ điển dữ liệu mô tả chi tiết các thành phần xuất hiện trên DFD: tác nhân ngoài, tiến trình, luồng dữ liệu và kho dữ liệu.')
u3('Tác nhân ngoài')
T.bang_du_lieu('Từ điển tác nhân ngoài', ['Tác nhân', 'Dữ liệu gửi vào hệ thống', 'Thông tin nhận từ hệ thống'], [
    ['Bệnh nhân', 'Đăng ký / đăng nhập; đăng ký khám chữa bệnh (đặt, đổi, hủy lịch)',
     'Lịch khám chữa bệnh; kết quả khám chữa bệnh (phiếu kết quả khám, đơn thuốc)'],
    ['Nhân viên', 'Tra cứu, tiếp nhận bệnh nhân; sắp xếp lịch khám chữa bệnh (đặt, đổi, hủy lịch hộ)', 'Danh sách lịch khám chữa bệnh'],
    ['Bác sĩ', 'Tra cứu lịch khám chữa bệnh; kết quả khám chữa bệnh (kết quả khám, chẩn đoán, chỉ định thuốc)',
     'Thông tin lịch khám chữa bệnh (hàng chờ, hồ sơ bệnh án, lịch làm việc)'],
    ['Quản trị', 'Quản lý tài khoản người dùng; quản lý lịch làm việc của bác sĩ; quản lý thông tin bác sĩ; danh mục thuốc; yêu cầu báo cáo',
     'Báo cáo thống kê (lượt khám, thuốc đã kê)'],
], [1900, 3586, 3586])
u3('Tiến trình')
T.bang_du_lieu('Từ điển tiến trình', ['Số hiệu', 'Tên tiến trình', 'Dữ liệu vào', 'Dữ liệu ra', 'Xử lý'], [
    ['1.1', 'Cập nhật thông tin bác sĩ', 'Quản lý thông tin bác sĩ; D1', 'D1', 'Kiểm tra họ tên, chuyên khoa, phòng, SĐT 10 chữ số; thêm hoặc sửa hồ sơ bác sĩ'],
    ['1.2', 'Cập nhật lịch làm việc', 'Quản lý lịch làm việc của bác sĩ; D1', 'D1; thông tin lịch khám chữa bệnh – lịch làm việc (Bác sĩ)',
     'Ghi các ca trong tuần: thứ, giờ bắt đầu, giờ kết thúc; các ca trong cùng một thứ không trùng giờ'],
    ['1.3', 'Cập nhật danh mục thuốc', 'Danh mục thuốc; D3', 'D3', 'Thêm thuốc với mã tự sinh THxxx hoặc sửa thông tin thuốc; tên và hàm lượng không trùng thuốc đã có'],
    ['1.4', 'Quản lý tài khoản người dùng', 'Quản lý tài khoản người dùng; đăng ký / đăng nhập; bác sĩ chưa có tài khoản (D1); D7', 'D7',
     'Tên đăng nhập không trùng, mật khẩu từ 6 ký tự, mã hóa một chiều; tài khoản bác sĩ gắn với một bác sĩ; khóa, mở khóa tài khoản'],
    ['2.1', 'Tiếp nhận yêu cầu đặt lịch', 'Đăng ký khám chữa bệnh (Bệnh nhân); sắp xếp lịch khám chữa bệnh (Nhân viên); D2',
     'Yêu cầu đặt lịch mới; yêu cầu đổi, hủy lịch; D2', 'Xác định bệnh nhân (nhân viên tìm hoặc thêm mới); chọn bác sĩ và ngày trong 7 ngày tới; phân loại yêu cầu'],
    ['2.2', 'Kiểm tra lịch trống', 'Yêu cầu đặt lịch mới; yêu cầu chọn giờ mới; lịch làm việc (D1); lịch đã đặt (D4)', 'Khung giờ trống',
     'Cắt ca làm việc thành khung 30 phút, loại khung đã có người giữ chỗ hoặc đã qua'],
    ['2.3', 'Xác nhận lịch hẹn', 'Khung giờ trống', 'D4; lịch khám chữa bệnh; danh sách lịch khám chữa bệnh',
     'Kiểm tra lại khung giờ, ghi lịch hẹn trạng thái Chưa đến kèm người đặt; khung vừa bị đặt thì mời chọn giờ khác'],
    ['2.4', 'Đổi, hủy lịch hẹn', 'Yêu cầu đổi, hủy lịch; D4', 'D4; yêu cầu chọn giờ mới; lịch khám chữa bệnh',
     'Chỉ với lịch Chưa đến, chưa tới giờ; hủy thì ghi lý do và trả lại khung giờ'],
    ['3.1', 'Tiếp nhận bệnh nhân', 'Tra cứu, tiếp nhận bệnh nhân; lịch hẹn trong ngày (D4); D2', 'D2; phiếu tiếp nhận (D4)',
     'Cập nhật hoặc tạo hồ sơ, cấp mã BN-xxxxx; chuyển lịch sang Chờ khám; người không hẹn được tạo lượt khám vãng lai'],
    ['3.2', 'Tra cứu lịch khám, lịch sử khám', 'Tra cứu lịch khám chữa bệnh; hàng chờ khám (D4); lịch sử khám (D5)',
     'Thông tin lịch khám chữa bệnh (hàng chờ, hồ sơ bệnh án)', 'Lấy hàng chờ của đúng bác sĩ đăng nhập; các lần khám trước của bệnh nhân, mới nhất trước'],
    ['3.3', 'Cập nhật bệnh án', 'Kết quả khám chữa bệnh', 'D5; D4 (Đã khám); kết quả khám chữa bệnh (Bệnh nhân)',
     'Bắt buộc có kết quả khám, chẩn đoán; ghi bệnh án, chuyển lịch sang Đã khám'],
    ['4.1', 'Lập đơn thuốc', 'Kết quả khám chữa bệnh (chỉ định thuốc); chẩn đoán (D5); thuốc cần điều chỉnh', 'Đơn thuốc dự kiến',
     'Chọn thuốc trong danh mục, nhập số lượng, cách dùng'],
    ['4.2', 'Kiểm tra thuốc', 'Đơn thuốc dự kiến; danh mục thuốc (D3)', 'Đơn thuốc hợp lệ; thuốc cần điều chỉnh',
     'Thuốc có trong danh mục, không trùng trong đơn, số lượng nguyên lớn hơn 0'],
    ['4.3', 'Lưu và in đơn thuốc', 'Đơn thuốc hợp lệ', 'D6; kết quả khám chữa bệnh – đơn thuốc (Bệnh nhân)',
     'Lưu đơn và các dòng thuốc cùng bệnh án trong một giao dịch; in đơn, gửi vào tài khoản bệnh nhân'],
    ['5.1', 'Thống kê lượt khám', 'Yêu cầu báo cáo; D5', 'Thống kê lượt khám', 'Đếm lượt khám theo ngày, theo bác sĩ trong kỳ (tối đa 366 ngày)'],
    ['5.2', 'Thống kê thuốc đã kê', 'Yêu cầu báo cáo; D6, D3', 'Thống kê thuốc đã kê', 'Đếm số đơn, cộng số lượng từng thuốc đã kê trong kỳ'],
], [760, 1700, 2150, 1900, 2562], giua={0}, co=22)
u3('Luồng dữ liệu')
T.bang_du_lieu('Từ điển luồng dữ liệu', ['Tên luồng', 'Nguồn → Đích', 'Thành phần dữ liệu'], [
    ['Đăng ký / đăng nhập', 'Bệnh nhân → 1.4', 'Họ tên, số điện thoại, mật khẩu khi đăng ký; số điện thoại, mật khẩu khi đăng nhập'],
    ['Đăng ký khám chữa bệnh', 'Bệnh nhân → 2.1', 'Bác sĩ, ngày, giờ, lý do khám; lịch cần đổi hoặc hủy, lý do hủy'],
    ['Lịch khám chữa bệnh', '2.3, 2.4 → Bệnh nhân', 'Thông báo đặt, đổi, hủy lịch: bác sĩ, phòng, ngày, giờ'],
    ['Kết quả khám chữa bệnh', '3.3, 4.3 → Bệnh nhân', 'Phiếu kết quả khám (chỉ số sinh tồn, kết quả, chẩn đoán, lời dặn); đơn thuốc (tên, hàm lượng, số lượng, cách dùng)'],
    ['Sắp xếp lịch khám chữa bệnh', 'Nhân viên → 2.1', 'Bệnh nhân (tìm theo SĐT, họ tên, mã; hoặc thêm mới: họ tên, SĐT), bác sĩ, ngày, giờ, lý do khám; lịch cần đổi, hủy'],
    ['Danh sách lịch khám chữa bệnh', '2.3 → Nhân viên', 'Ngày, giờ, bệnh nhân, bác sĩ, lý do khám, trạng thái, người đặt'],
    ['Tra cứu, tiếp nhận bệnh nhân', 'Nhân viên → 3.1', 'SĐT, họ tên hoặc mã để tra cứu; họ tên, năm sinh, giới tính, địa chỉ, lý do khám; bác sĩ khám (khi không hẹn trước)'],
    ['Tra cứu lịch khám chữa bệnh', 'Bác sĩ → 3.2', 'Bác sĩ đăng nhập, bệnh nhân cần xem hồ sơ'],
    ['Thông tin lịch khám chữa bệnh', '3.2, 1.2 → Bác sĩ', 'Hàng chờ khám theo giờ; các lần khám trước của bệnh nhân; các ca làm việc trong tuần, lịch hẹn 7 ngày tới'],
    ['Kết quả khám chữa bệnh', 'Bác sĩ → 3.3, 4.1', 'Mạch, nhiệt độ, huyết áp, cân nặng, kết quả khám, chẩn đoán, lời dặn; chỉ định thuốc (thuốc, số lượng, cách dùng)'],
    ['Quản lý thông tin bác sĩ', 'Quản trị → 1.1', 'Họ tên, chuyên khoa, phòng, SĐT, email, trình độ'],
    ['Quản lý lịch làm việc của bác sĩ', 'Quản trị → 1.2', 'Bác sĩ, các ca: thứ, giờ bắt đầu, giờ kết thúc'],
    ['Danh mục thuốc', 'Quản trị → 1.3', 'Tên thuốc, hàm lượng, đơn vị, nhóm, liều tối đa, lưu ý'],
    ['Quản lý tài khoản người dùng', 'Quản trị → 1.4', 'Họ tên, tên đăng nhập, vai trò, bác sĩ liên kết, mật khẩu; yêu cầu khóa, mở khóa'],
    ['Yêu cầu báo cáo', 'Quản trị → 5.1, 5.2', 'Loại báo cáo, từ ngày, đến ngày'],
    ['Báo cáo thống kê', '5.1, 5.2 → Quản trị', 'Lượt khám theo ngày, theo bác sĩ; số đơn, số lượng từng thuốc đã kê'],
    ['Khung giờ trống', '2.2 → 2.3', 'Bác sĩ, ngày, các giờ còn trống'],
    ['Phiếu tiếp nhận', '3.1 → D4', 'Lịch hẹn, mã bệnh nhân, bác sĩ khám, lý do khám'],
    ['Đơn thuốc dự kiến', '4.1 → 4.2', 'Các dòng thuốc chưa kiểm tra'],
    ['Thuốc cần điều chỉnh', '4.2 → 4.1', 'Dòng thuốc chưa hợp lệ và lý do'],
    ['Đơn thuốc hợp lệ', '4.2 → 4.3', 'Các dòng thuốc đã qua kiểm tra'],
], [2500, 2100, 4472], co=22)
u3('Kho dữ liệu')
T.bang_du_lieu('Từ điển kho dữ liệu', ['Kho dữ liệu', 'Thành phần dữ liệu', 'Tiến trình ghi', 'Tiến trình đọc'], [
    ['D1 Hồ sơ bác sĩ', 'Mã bác sĩ, họ tên, chuyên khoa, phòng, SĐT, email, trình độ; các ca làm việc (thứ, giờ bắt đầu, giờ kết thúc)', '1.1, 1.2', '1.2, 1.4, 2.2'],
    ['D2 Hồ sơ bệnh nhân', 'Mã bệnh nhân, họ tên, SĐT, năm sinh, giới tính, địa chỉ', '2.1, 3.1', '2.1, 3.1'],
    ['D3 Danh mục thuốc', 'Mã thuốc, tên, hàm lượng, đơn vị, nhóm, liều tối đa, lưu ý', '1.3', '1.3, 4.2, 5.2'],
    ['D4 Lịch hẹn', 'Mã lịch hẹn, bệnh nhân, bác sĩ, ngày, giờ, loại, lý do khám, trạng thái, lý do hủy, người đặt', '2.3, 2.4, 3.1, 3.3', '2.2, 2.4, 3.1, 3.2'],
    ['D5 Hồ sơ bệnh án', 'Mã bệnh án, lịch hẹn, ngày khám, chỉ số sinh tồn, kết quả khám, chẩn đoán, lời dặn', '3.3', '3.2, 4.1, 5.1'],
    ['D6 Đơn thuốc', 'Mã đơn, bệnh án, ngày kê; các dòng thuốc (thuốc, số lượng, cách dùng)', '4.3', '3.2, 5.2'],
    ['D7 Tài khoản người dùng', 'Tên đăng nhập, mật khẩu, vai trò, họ tên, bệnh nhân hoặc bác sĩ liên kết, trạng thái hoạt động', '1.4', '1.4; đăng nhập'],
], [2100, 4172, 1400, 1400], giua={2, 3})

# =====================================================================================
# CHƯƠNG 3
# =====================================================================================
u1('Mô hình hóa dữ liệu (ERD)')
u2('Xác định các thực thể và thuộc tính')
p('Từ các kho dữ liệu và luồng dữ liệu ở Chương 2, xác định được 9 thực thể. Thuộc tính khóa được gạch chân.')
T.bang_du_lieu('Các thực thể và thuộc tính', ['Thực thể', 'Thuộc tính', 'Ý nghĩa'], [
    ['BÁC SĨ', '__Mã bác sĩ__, Họ tên, Chuyên khoa, Phòng, Số điện thoại, Email, Trình độ', 'Bác sĩ làm việc tại phòng khám (D1)'],
    ['CA LÀM VIỆC', '__Mã ca__, Thứ, Giờ bắt đầu, Giờ kết thúc', 'Một ca làm việc trong tuần của bác sĩ (D1)'],
    ['BỆNH NHÂN', '__Mã bệnh nhân__, Họ tên, Số điện thoại, Năm sinh, Giới tính, Địa chỉ', 'Người đến khám (D2)'],
    ['TÀI KHOẢN', '__Tên đăng nhập__, Mật khẩu, Vai trò, Họ tên, Trạng thái hoạt động', 'Tài khoản đăng nhập của một trong bốn vai trò (D7)'],
    ['THUỐC', '__Mã thuốc__, Tên thuốc, Hàm lượng, Đơn vị, Nhóm, Liều tối đa, Lưu ý', 'Thuốc trong danh mục, bác sĩ chọn khi kê đơn (D3)'],
    ['LỊCH HẸN', '__Mã lịch hẹn__, Ngày, Giờ, Loại, Lý do khám, Trạng thái, Lý do hủy, Người đặt', 'Một lần hẹn khám hoặc một lượt khám vãng lai (D4)'],
    ['BỆNH ÁN', '__Mã bệnh án__, Ngày khám, Mạch, Nhiệt độ, Huyết áp, Cân nặng, Kết quả khám, Chẩn đoán, Lời dặn', 'Kết quả một lần khám (D5)'],
    ['ĐƠN THUỐC', '__Mã đơn thuốc__, Ngày kê', 'Đơn thuốc của một lần khám, in cho bệnh nhân (D6)'],
    ['THÔNG BÁO', '__Mã thông báo__, Loại, Tiêu đề, Nội dung, Thời điểm', 'Thông báo gửi tới bệnh nhân'],
], [2000, 4400, 2672])
p('Các mối quan hệ giữa các thực thể như sau.')
T.bang_du_lieu('Các mối quan hệ giữa các thực thể', ['Mối quan hệ', 'Thực thể tham gia', 'Bản số', 'Ý nghĩa'], [
    ['Làm việc theo', 'BÁC SĨ – CA LÀM VIỆC', '1 – n', 'Một bác sĩ có nhiều ca làm việc trong tuần'],
    ['Khám theo', 'BÁC SĨ – LỊCH HẸN', '1 – n', 'Một bác sĩ nhận nhiều lịch hẹn'],
    ['Gắn với', 'BÁC SĨ – TÀI KHOẢN', '1 – 0..1', 'Mỗi bác sĩ có tối đa một tài khoản để đăng nhập'],
    ['Đặt', 'BỆNH NHÂN – LỊCH HẸN', '1 – n', 'Một bệnh nhân có nhiều lịch hẹn'],
    ['Sở hữu', 'BỆNH NHÂN – TÀI KHOẢN', '1 – 0..1', 'Một bệnh nhân có tối đa một tài khoản'],
    ['Nhận', 'BỆNH NHÂN – THÔNG BÁO', '1 – n', 'Một bệnh nhân nhận nhiều thông báo'],
    ['Sinh ra', 'LỊCH HẸN – BỆNH ÁN', '1 – 0..1', 'Mỗi lịch hẹn đã khám sinh ra đúng một bệnh án'],
    ['Có', 'BỆNH ÁN – ĐƠN THUỐC', '1 – 0..1', 'Mỗi bệnh án có tối đa một đơn thuốc'],
    ['Gồm', 'ĐƠN THUỐC – THUỐC', 'n – n', 'Một đơn gồm nhiều thuốc, một thuốc có trong nhiều đơn; thuộc tính riêng: Số lượng, Cách dùng'],
], [1500, 2900, 1100, 3572], giua={2})
u2('Lược đồ thực thể kết hợp (ERD)')
T.hinh_ngang('Lược đồ thực thể kết hợp dưới đây thể hiện 9 thực thể (hình chữ nhật, thuộc tính khóa gạch chân), 9 mối quan hệ (hình '
             'thoi) và bản số trên mỗi nhánh. Mối quan hệ Gồm giữa ĐƠN THUỐC và THUỐC là quan hệ nhiều – nhiều, mang hai thuộc tính '
             'Số lượng và Cách dùng. Thực thể TÀI KHOẢN dùng chung cho bốn vai trò.',
             HINH + '09-ERD-luoc-do-thuc-the-ket-hop.png', 'Lược đồ thực thể kết hợp (ERD) của hệ thống', rong=25, cao=15.5)
u2('Ma trận thực thể – chức năng (CRUD)')
p('Ma trận CRUD cho biết mỗi chức năng tác động lên thực thể nào: C – thêm mới (Create), R – đọc (Read), U – sửa (Update), D – xóa '
  '(Delete). Tên cột viết tắt: BS – Bác sĩ, CA – Ca làm việc, BN – Bệnh nhân, TK – Tài khoản, TH – Thuốc, LH – Lịch hẹn, BA – Bệnh '
  'án, ĐT – Đơn thuốc, CT – Chi tiết đơn thuốc (quan hệ Gồm), TB – Thông báo.')
COT = ['BS', 'CA', 'BN', 'TK', 'TH', 'LH', 'BA', 'ĐT', 'CT', 'TB']
CRUD = [
    ('Đăng ký tài khoản', {'BN': 'CR', 'TK': 'C'}),
    ('Đăng nhập', {'BN': 'R', 'TK': 'R'}),
    ('1.1 Cập nhật thông tin bác sĩ', {'BS': 'CRU'}),
    ('1.2 Cập nhật lịch làm việc', {'BS': 'R', 'CA': 'CRD'}),
    ('1.3 Cập nhật danh mục thuốc', {'TH': 'CRU'}),
    ('1.4 Quản lý tài khoản người dùng', {'BS': 'R', 'TK': 'CRU'}),
    ('2.1 Tiếp nhận yêu cầu đặt lịch', {'BS': 'R', 'BN': 'CR'}),
    ('2.2 Kiểm tra lịch trống', {'BS': 'R', 'CA': 'R', 'LH': 'R'}),
    ('2.3 Xác nhận lịch hẹn', {'BS': 'R', 'BN': 'R', 'LH': 'CR', 'TB': 'C'}),
    ('2.4 Đổi, hủy lịch hẹn', {'BS': 'R', 'LH': 'RU', 'TB': 'C'}),
    ('3.1 Tiếp nhận bệnh nhân', {'BS': 'R', 'BN': 'CRU', 'LH': 'CRU'}),
    ('3.2 Tra cứu lịch khám, lịch sử khám', {'BS': 'R', 'BN': 'R', 'TH': 'R', 'LH': 'R', 'BA': 'R', 'ĐT': 'R', 'CT': 'R'}),
    ('3.3 Cập nhật bệnh án', {'BN': 'R', 'LH': 'RU', 'BA': 'C', 'TB': 'C'}),
    ('4.1 Lập đơn thuốc', {'TH': 'R', 'BA': 'R'}),
    ('4.2 Kiểm tra thuốc', {'TH': 'R'}),
    ('4.3 Lưu và in đơn thuốc', {'BN': 'R', 'TH': 'R', 'BA': 'R', 'ĐT': 'C', 'CT': 'C', 'TB': 'C'}),
    ('5.1 Thống kê lượt khám', {'BS': 'R', 'BA': 'R'}),
    ('5.2 Thống kê thuốc đã kê', {'TH': 'R', 'ĐT': 'R', 'CT': 'R'}),
]
T.bang_du_lieu('Ma trận thực thể – chức năng (CRUD)', ['Chức năng'] + COT,
               [[ten] + [m.get(c, '') for c in COT] for ten, m in CRUD],
               [2912] + [616] * 10, giua=set(range(1, 11)), co=20)
p('Ma trận cho thấy mọi thực thể đều có ít nhất một chức năng tạo ra (C) và một chức năng sử dụng (R). Bác sĩ và thuốc chỉ được thêm, '
  'sửa mà không xóa để giữ nguyên lịch sử khám và đơn thuốc đã kê; tài khoản không bị xóa mà được khóa; ca làm việc được xóa và khai '
  'báo lại khi bác sĩ đổi lịch.')

# =====================================================================================
# CHƯƠNG 4
# =====================================================================================
u1('Thiết kế cơ sở dữ liệu và giao diện')
u2('Lược đồ CSDL quan hệ')
p('Lược đồ thực thể kết hợp được chuyển thành lược đồ quan hệ theo các quy tắc sau:')
gach('Mỗi thực thể thành một bảng; mỗi bảng có khóa chính là cột số nguyên tự tăng. Các mã nghiệp vụ (mã bệnh nhân BN-xxxxx, mã thuốc '
     'THxxx, tên đăng nhập) được giữ làm khóa duy nhất.')
gach('Quan hệ 1 – n: khóa chính của bảng phía 1 đặt làm khóa ngoại ở bảng phía n (ví dụ bac_si_id trong lich_hen, benh_nhan_id trong '
     'thong_bao).')
gach('Quan hệ 1 – 0..1: khóa ngoại đặt ở bảng phía 0..1 và có ràng buộc duy nhất (lich_hen_id trong benh_an, benh_an_id trong '
     'don_thuoc, benh_nhan_id và bac_si_id trong tai_khoan).')
gach('Quan hệ n – n Gồm thành bảng riêng chi_tiet_don_thuoc, chứa hai khóa ngoại và hai thuộc tính số lượng, cách dùng; cặp (đơn '
     'thuốc, thuốc) là duy nhất nên một thuốc không xuất hiện hai lần trong một đơn.')
gach('Bảng benh_an lưu thêm benh_nhan_id, bac_si_id (lấy từ lịch hẹn) để tra cứu lịch sử và thống kê nhanh.')
p('Kết quả chuyển đổi (khóa chính in đậm, khóa ngoại in nghiêng):')
for dong in ['bac_si (**id**, ho_ten, chuyen_khoa, phong, so_dien_thoai, email, trinh_do)',
             'ca_lam_viec (**id**, *bac_si_id*, thu, gio_bat_dau, gio_ket_thuc)',
             'benh_nhan (**id**, ma, ho_ten, so_dien_thoai, nam_sinh, gioi_tinh, dia_chi, tao_luc)',
             'tai_khoan (**id**, ten_dang_nhap, mat_khau, vai_tro, ho_ten, *benh_nhan_id*, *bac_si_id*, hoat_dong, tao_luc)',
             'thuoc (**id**, ma, ten, ham_luong, don_vi, nhom, lieu_toi_da, luu_y)',
             'lich_hen (**id**, *benh_nhan_id*, *bac_si_id*, ngay, gio, loai, ly_do, trang_thai, ly_do_huy, dat_boi, tao_luc, giu_cho)',
             'benh_an (**id**, *lich_hen_id*, *benh_nhan_id*, *bac_si_id*, ngay_kham, mach, nhiet_do, huyet_ap, can_nang, ket_qua, chan_doan, loi_dan, tao_luc)',
             'don_thuoc (**id**, *benh_an_id*, tao_luc)',
             'chi_tiet_don_thuoc (**id**, *don_thuoc_id*, *thuoc_id*, so_luong, cach_dung)',
             'thong_bao (**id**, *benh_nhan_id*, loai, tieu_de, noi_dung, tao_luc)']:
    gach(dong)
p('Mỗi kho dữ liệu của DFD tương ứng với một hoặc hai bảng như bảng sau.')
T.bang_du_lieu('Ánh xạ kho dữ liệu sang bảng CSDL', ['Kho dữ liệu', 'Bảng', 'Nội dung'], [
    ['D1 Hồ sơ bác sĩ', 'bac_si, ca_lam_viec', 'Thông tin bác sĩ; các ca làm việc trong tuần'],
    ['D2 Hồ sơ bệnh nhân', 'benh_nhan', 'Hồ sơ bệnh nhân'],
    ['D3 Danh mục thuốc', 'thuoc', 'Thuốc bác sĩ được chọn khi kê đơn'],
    ['D4 Lịch hẹn', 'lich_hen', 'Lịch hẹn và lượt khám vãng lai'],
    ['D5 Hồ sơ bệnh án', 'benh_an', 'Kết quả mỗi lần khám'],
    ['D6 Đơn thuốc', 'don_thuoc, chi_tiet_don_thuoc', 'Đơn thuốc và các thuốc trong đơn'],
    ['D7 Tài khoản người dùng', 'tai_khoan', 'Tài khoản đăng nhập của bốn vai trò'],
    ['(bổ sung)', 'thong_bao', 'Thông báo gửi bệnh nhân'],
], [2700, 2700, 3672])
T.hinh_ngang('Hình tiếp theo là lược đồ quan hệ giữa các bảng: mỗi bảng ghi tên kho dữ liệu tương ứng, các đường nối khóa ngoại '
             'với khóa chính kèm bản số.',
             HINH + '10-luoc-do-csdl-quan-he.png', 'Lược đồ cơ sở dữ liệu quan hệ', rong=25, cao=15.5)
u2('Bảng mô tả chi tiết các bảng CSDL')
p('Cơ sở dữ liệu dùng hệ quản trị MySQL, bảng mã utf8mb4 để lưu tiếng Việt. Các bảng được mô tả chi tiết dưới đây.')
TD = ['Cột', 'Kiểu dữ liệu', 'Ràng buộc', 'Ý nghĩa']
RB = [2150, 2300, 2300, 2322]
BANG_CSDL = [
    ('Bảng bac_si – hồ sơ bác sĩ (D1)', [
        ['id', 'INT UNSIGNED', 'PK', 'Mã bác sĩ'], ['ho_ten', 'VARCHAR(100)', 'NOT NULL', 'Họ và tên'],
        ['chuyen_khoa', 'VARCHAR(50)', 'NOT NULL', 'Chuyên khoa'], ['phong', 'VARCHAR(20)', 'NOT NULL', 'Phòng khám'],
        ['so_dien_thoai', 'CHAR(10)', 'NOT NULL', 'Số điện thoại'], ['email', 'VARCHAR(100)', '', 'Thư điện tử'],
        ['trinh_do', 'VARCHAR(100)', '', 'Trình độ chuyên môn']]),
    ('Bảng ca_lam_viec – ca làm việc của bác sĩ (D1)', [
        ['id', 'INT UNSIGNED', 'PK', 'Mã ca'], ['bac_si_id', 'INT UNSIGNED', 'FK → bac_si', 'Bác sĩ làm ca này'],
        ['thu', 'TINYINT', 'NOT NULL', '1 = Thứ Hai … 7 = Chủ nhật'], ['gio_bat_dau', 'TIME', 'NOT NULL', 'Giờ bắt đầu ca'],
        ['gio_ket_thuc', 'TIME', 'NOT NULL', 'Giờ kết thúc ca']]),
    ('Bảng benh_nhan – hồ sơ bệnh nhân (D2)', [
        ['id', 'INT UNSIGNED', 'PK', 'Mã nội bộ'], ['ma', 'VARCHAR(10)', 'UNIQUE', 'Mã bệnh nhân BN-xxxxx, cấp khi tiếp nhận lần đầu'],
        ['ho_ten', 'VARCHAR(100)', 'NOT NULL', 'Họ và tên'], ['so_dien_thoai', 'CHAR(10)', 'NOT NULL, UNIQUE', 'Số điện thoại'],
        ['nam_sinh', 'SMALLINT', '', 'Năm sinh'], ['gioi_tinh', "ENUM('Nam','Nữ')", '', 'Giới tính'],
        ['dia_chi', 'VARCHAR(255)', '', 'Địa chỉ'], ['tao_luc', 'DATETIME', 'NOT NULL', 'Thời điểm tạo hồ sơ']]),
    ('Bảng tai_khoan – tài khoản người dùng (D7)', [
        ['id', 'INT UNSIGNED', 'PK', 'Mã tài khoản'],
        ['ten_dang_nhap', 'VARCHAR(50)', 'NOT NULL, UNIQUE', 'Tên đăng nhập; bệnh nhân dùng số điện thoại'],
        ['mat_khau', 'VARCHAR(255)', 'NOT NULL', 'Mật khẩu đã mã hóa một chiều'],
        ['vai_tro', "ENUM('QT','NV','BS','BN')", 'NOT NULL', 'Quản trị, Nhân viên, Bác sĩ, Bệnh nhân'],
        ['ho_ten', 'VARCHAR(100)', '', 'Tên người dùng do quản trị cấp; bệnh nhân lấy tên từ benh_nhan'],
        ['benh_nhan_id', 'INT UNSIGNED', 'FK → benh_nhan, UNIQUE', 'Hồ sơ bệnh nhân gắn với tài khoản bệnh nhân'],
        ['bac_si_id', 'INT UNSIGNED', 'FK → bac_si, UNIQUE', 'Hồ sơ bác sĩ gắn với tài khoản bác sĩ'],
        ['hoat_dong', 'TINYINT(1)', 'NOT NULL, mặc định 1', '0 khi tài khoản bị khóa, không đăng nhập được'],
        ['tao_luc', 'DATETIME', 'NOT NULL', 'Thời điểm tạo tài khoản']]),
    ('Bảng thuoc – danh mục thuốc (D3)', [
        ['id', 'INT UNSIGNED', 'PK', 'Mã nội bộ'], ['ma', 'VARCHAR(10)', 'NOT NULL, UNIQUE', 'Mã thuốc THxxx'],
        ['ten', 'VARCHAR(100)', 'NOT NULL', 'Tên thuốc'], ['ham_luong', 'VARCHAR(30)', 'NOT NULL', 'Hàm lượng, ví dụ 500 mg'],
        ['don_vi', 'VARCHAR(20)', 'NOT NULL', 'Đơn vị tính: Viên, Gói, Chai…'], ['nhom', 'VARCHAR(50)', 'NOT NULL', 'Nhóm thuốc'],
        ['lieu_toi_da', 'VARCHAR(50)', '', 'Liều tối đa một ngày'], ['luu_y', 'VARCHAR(500)', '', 'Lưu ý, chống chỉ định']]),
    ('Bảng lich_hen – lịch hẹn (D4)', [
        ['id', 'INT UNSIGNED', 'PK', 'Mã lịch hẹn'], ['benh_nhan_id', 'INT UNSIGNED', 'FK → benh_nhan', 'Bệnh nhân'],
        ['bac_si_id', 'INT UNSIGNED', 'FK → bac_si', 'Bác sĩ khám'], ['ngay, gio', 'DATE, TIME', 'NOT NULL', 'Ngày và giờ hẹn'],
        ['loai', 'ENUM', 'NOT NULL', 'hen_truoc: đặt trước; vang_lai: đến khám không hẹn'], ['ly_do', 'VARCHAR(255)', '', 'Lý do khám'],
        ['trang_thai', 'ENUM', 'NOT NULL', 'da_dat, cho_kham, dang_kham, da_kham, da_huy'], ['ly_do_huy', 'VARCHAR(100)', '', 'Lý do hủy'],
        ['dat_boi', 'ENUM', 'NOT NULL', 'benh_nhan: bệnh nhân tự đặt; nhan_vien: nhân viên đặt hộ'],
        ['tao_luc', 'DATETIME', 'NOT NULL', 'Thời điểm đặt'],
        ['giu_cho', 'TINYINT (cột sinh)', 'UNIQUE cùng bac_si_id, ngay, gio',
         '= 1 khi lịch đang giữ khung giờ; rỗng khi đã hủy hoặc vãng lai, nhờ đó một khung giờ chỉ có một lịch giữ chỗ']]),
    ('Bảng benh_an – hồ sơ bệnh án (D5)', [
        ['id', 'INT UNSIGNED', 'PK', 'Mã bệnh án'], ['lich_hen_id', 'INT UNSIGNED', 'FK → lich_hen, UNIQUE', 'Lượt khám sinh ra bệnh án'],
        ['benh_nhan_id, bac_si_id', 'INT UNSIGNED', 'FK', 'Bệnh nhân, bác sĩ khám'], ['ngay_kham', 'DATE', 'NOT NULL', 'Ngày khám'],
        ['mach, nhiet_do, huyet_ap, can_nang', 'VARCHAR', '', 'Chỉ số sinh tồn'], ['ket_qua', 'TEXT', 'NOT NULL', 'Kết quả khám'],
        ['chan_doan', 'VARCHAR(255)', 'NOT NULL', 'Chẩn đoán'], ['loi_dan', 'TEXT', '', 'Lời dặn của bác sĩ'],
        ['tao_luc', 'DATETIME', 'NOT NULL', 'Thời điểm lưu']]),
    ('Bảng don_thuoc – đơn thuốc (D6)', [
        ['id', 'INT UNSIGNED', 'PK', 'Mã đơn thuốc'], ['benh_an_id', 'INT UNSIGNED', 'FK → benh_an, UNIQUE', 'Bệnh án của đơn (mỗi bệnh án tối đa một đơn)'],
        ['tao_luc', 'DATETIME', 'NOT NULL', 'Thời điểm kê đơn']]),
    ('Bảng chi_tiet_don_thuoc – thuốc trong đơn (D6)', [
        ['id', 'INT UNSIGNED', 'PK', 'Mã dòng'], ['don_thuoc_id', 'INT UNSIGNED', 'FK → don_thuoc', 'Đơn thuốc'],
        ['thuoc_id', 'INT UNSIGNED', 'FK → thuoc', 'Thuốc trong danh mục'], ['so_luong', 'SMALLINT UNSIGNED', 'NOT NULL', 'Số lượng'],
        ['cach_dung', 'VARCHAR(255)', '', 'Cách dùng'], ['(don_thuoc_id, thuoc_id)', '', 'UNIQUE', 'Một thuốc không xuất hiện hai lần trong cùng đơn']]),
    ('Bảng thong_bao – thông báo cho bệnh nhân', [
        ['id', 'INT UNSIGNED', 'PK', 'Mã thông báo'], ['benh_nhan_id', 'INT UNSIGNED', 'FK → benh_nhan', 'Bệnh nhân nhận'],
        ['loai', 'ENUM', 'NOT NULL', 'dat_lich, doi_lich, huy_lich, ket_qua, don_thuoc'],
        ['tieu_de, noi_dung', 'VARCHAR', 'NOT NULL', 'Tiêu đề và nội dung'], ['tao_luc', 'DATETIME', 'NOT NULL', 'Thời điểm gửi']]),
]
for ten, hang in BANG_CSDL:
    T.bang_du_lieu(ten, TD, hang, RB)
u2('Bảng phân quyền người dùng')
p('Hệ thống có bốn vai trò lưu trong cột vai_tro của bảng tai_khoan: QT – Quản trị, NV – Nhân viên, BS – Bác sĩ và BN – Bệnh nhân. '
  'Người chưa đăng nhập chỉ được đăng ký và đăng nhập. Mỗi trang kiểm tra vai trò của người đăng nhập; người dùng sai vai trò được '
  'chuyển về trang chủ của mình, tài khoản bị khóa không đăng nhập được.')
V, K = '✓', '–'
PQ = [  # chức năng, chưa đăng nhập, BN, NV, BS, QT
    ('Đăng ký tài khoản bằng số điện thoại', V, K, K, K, K),
    ('Đăng nhập', V, K, K, K, K),
    ('Đặt, đổi, hủy lịch hẹn của mình (CN 2.1–2.4)', K, V, K, K, K),
    ('Xem thông báo, kết quả khám, đơn thuốc của mình', K, V, K, K, K),
    ('Sắp xếp lịch hộ: đặt, đổi, hủy; thêm bệnh nhân mới (CN 2.1–2.4)', K, K, V, K, K),
    ('Tra cứu, tiếp nhận bệnh nhân, khám không hẹn trước (CN 3.1)', K, K, V, K, K),
    ('Xem danh sách lịch hẹn', K, K, V, K, K),
    ('Xem hàng chờ khám của mình, gọi vào khám (CN 3.2)', K, K, K, V, K),
    ('Xem hồ sơ bệnh án các lần trước (CN 3.2)', K, K, K, V, K),
    ('Ghi kết quả khám, kê đơn (CN 3.3, 4.1–4.3)', K, K, K, V, K),
    ('Xem lịch làm việc của mình (CN 1.2)', K, K, K, V, K),
    ('In phiếu kết quả khám, đơn thuốc (bệnh nhân: phiếu của mình)', K, V, V, V, V),
    ('Quản lý bác sĩ, lịch làm việc (CN 1.1, 1.2)', K, K, K, K, V),
    ('Quản lý danh mục thuốc (CN 1.3)', K, K, K, K, V),
    ('Quản lý tài khoản người dùng (CN 1.4)', K, K, K, K, V),
    ('Xem tình hình trong ngày, báo cáo thống kê (CN 5.1, 5.2)', K, K, K, K, V),
]
T.bang_du_lieu('Phân quyền người dùng theo chức năng',
               ['Chức năng', 'Chưa đăng nhập', 'Bệnh nhân', 'Nhân viên', 'Bác sĩ', 'Quản trị'],
               [list(h) for h in PQ], [3472] + [1120] * 5, giua=set(range(1, 6)), co=22)
p('Ngoài giới hạn theo chức năng, dữ liệu cũng được giới hạn theo người: bệnh nhân chỉ xem, đổi, hủy được lịch hẹn và kết quả khám '
  'của chính mình (lọc theo mã bệnh nhân gắn với tài khoản); bác sĩ chỉ xem hàng chờ và ghi bệnh án cho bệnh nhân hẹn khám với mình '
  '(lọc theo bác sĩ gắn với tài khoản); nhân viên và quản trị làm việc với dữ liệu của mọi bệnh nhân trong phạm vi chức năng của mình.')
u2('Thiết kế giao diện minh họa')
u3('Nguyên tắc thiết kế')
gach('**Màu sắc, chữ:** nền xám nhạt, màu chủ đạo xanh ngọc #0E7C70, phông chữ Lexend cỡ 15 px dễ đọc; trạng thái lịch hẹn phân '
     'biệt bằng nhãn màu.')
gach('**Bố cục:** thanh điều hướng luôn nằm trên cùng, hiện tên và vai trò người đăng nhập; mỗi vai trò có thực đơn riêng: Bệnh nhân '
     '(Trang chủ, Đặt lịch khám, Kết quả khám), Nhân viên (Trang chủ, Tiếp nhận, Đặt lịch hộ, Lịch hẹn), Bác sĩ (Hàng chờ khám, Lịch '
     'làm việc), Quản trị (Trang chủ, Bác sĩ, Thuốc, Tài khoản, Báo cáo).')
gach('**Mỗi màn hình một việc:** các màn hình đánh số [1] đến [27] theo luồng người dùng, mỗi màn gắn với một chức năng con trong BFD; '
     'các màn đặt lịch [15]–[17] và hủy lịch [19] dùng chung cho bệnh nhân và nhân viên, khi nhân viên đặt hộ thì đầu màn có dải ghi '
     'rõ đang đặt cho bệnh nhân nào.')
gach('**Phản hồi rõ ràng:** dữ liệu nhập sai được báo ngay dưới ô nhập; khung giờ đã có người đặt không chọn được; thuốc trùng trong '
     'đơn bị báo lỗi tức thì.')
gach('**In ấn:** phiếu kết quả khám, đơn thuốc và báo cáo trình bày theo khổ A4, in hoặc lưu PDF ngay trên trang.')
u3('Sơ đồ luồng màn hình')
T.hinh_ngang('Userflow tổng quan bắt đầu từ màn hình đăng nhập [1]; tài khoản bị khóa bị chặn lại, còn lại hệ thống chuyển người dùng '
             'về trang chủ theo vai trò: Bệnh nhân vào [3], Nhân viên vào [4], Bác sĩ vào hàng chờ khám [5], Quản trị vào [6]; từ đó '
             'đi vào bốn luồng công việc.',
             HINH + '11-userflow-tong-quan.png', 'Userflow tổng quan của hệ thống', rong=25, cao=15.5)
p('Luồng 1 dành cho quản trị: danh sách bác sĩ [7], thêm, sửa bác sĩ [8], lịch làm việc [9], danh mục thuốc [10], thêm, sửa thuốc '
  '[11], tài khoản người dùng [12] và biểu mẫu tài khoản [13]. Luồng 2 dùng chung cho bệnh nhân tự đặt và nhân viên đặt hộ: nhân viên '
  'bắt đầu ở [14] để chọn hoặc thêm bệnh nhân, sau đó cả hai đi qua [15] chọn bác sĩ, ngày, [16] chọn giờ, [17] xác nhận; đổi, hủy đi '
  'qua [18] (nhân viên dùng lịch sắp tới ở [14] hoặc danh sách lịch hẹn [22]) và [19].')
T.hinh_anh(HINH + '12-userflow-luong-1-quan-tri-he-thong.png', 'Userflow luồng 1 – Quản trị hệ thống', rong=16)
T.hinh_anh(HINH + '13-userflow-luong-2-dat-doi-huy-lich-hen.png', 'Userflow luồng 2 – Đặt, đổi, hủy lịch hẹn', cao=21)
p('Luồng 3 nối công việc của nhân viên và bác sĩ: nhân viên tiếp nhận ở [21] hoặc từ danh sách lịch hẹn [22], bệnh nhân vào hàng chờ '
  '[5] của bác sĩ; bác sĩ gọi vào khám, xem hồ sơ các lần trước [23], ghi kết quả và kê đơn [24]; phiếu kết quả khám và đơn thuốc được '
  'in để bệnh nhân mang ra nhà thuốc mua thuốc. Luồng 4 là báo cáo của quản trị: chọn loại báo cáo [26] và xem kết quả [27].')
T.hinh_anh(HINH + '14-userflow-luong-3-tiep-nhan-kham-ke-don.png', 'Userflow luồng 3 – Tiếp nhận, khám bệnh, kê đơn', cao=21)
T.hinh_anh(HINH + '15-userflow-luong-4-bao-cao-thong-ke.png', 'Userflow luồng 4 – Báo cáo thống kê', rong=14)
p('Danh sách các màn hình được tổng hợp trong bảng sau.')
MAN = [
    ('[1]', 'Đăng nhập', 'Mọi vai trò', ''), ('[2]', 'Đăng ký tài khoản', 'Bệnh nhân', '1.4'),
    ('[3]', 'Trang chủ Bệnh nhân', 'Bệnh nhân', ''), ('[4]', 'Trang chủ Nhân viên', 'Nhân viên', ''),
    ('[5]', 'Hàng chờ khám', 'Bác sĩ', '3.2'), ('[6]', 'Trang chủ Quản trị', 'Quản trị', ''),
    ('[7]', 'Danh sách bác sĩ', 'Quản trị', '1.1'), ('[8]', 'Thêm, sửa bác sĩ', 'Quản trị', '1.1'),
    ('[9]', 'Lịch làm việc bác sĩ', 'Quản trị', '1.2'), ('[10]', 'Danh mục thuốc', 'Quản trị', '1.3'),
    ('[11]', 'Thêm, sửa thuốc', 'Quản trị', '1.3'), ('[12]', 'Tài khoản người dùng', 'Quản trị', '1.4'),
    ('[13]', 'Thêm, sửa tài khoản', 'Quản trị', '1.4'), ('[14]', 'Đặt lịch hộ – chọn bệnh nhân', 'Nhân viên', '2.1'),
    ('[15]', 'Chọn bác sĩ, ngày khám', 'Bệnh nhân, Nhân viên', '2.1'), ('[16]', 'Chọn khung giờ trống', 'Bệnh nhân, Nhân viên', '2.2'),
    ('[17]', 'Xác nhận lịch hẹn', 'Bệnh nhân, Nhân viên', '2.3'), ('[18]', 'Lịch hẹn của tôi', 'Bệnh nhân', '2.4'),
    ('[19]', 'Xác nhận hủy lịch hẹn', 'Bệnh nhân, Nhân viên', '2.4'), ('[20]', 'Kết quả khám, đơn thuốc', 'Bệnh nhân', '3.3, 4.3'),
    ('[21]', 'Tiếp nhận bệnh nhân', 'Nhân viên', '3.1'), ('[22]', 'Danh sách lịch hẹn', 'Nhân viên', '2.4, 3.1'),
    ('[23]', 'Hồ sơ bệnh án các lần trước', 'Bác sĩ', '3.2'), ('[24]', 'Ghi kết quả khám và kê đơn', 'Bác sĩ', '3.3, 4.1–4.3'),
    ('[25]', 'Lịch làm việc của tôi', 'Bác sĩ', '1.2'), ('[26]', 'Chọn loại báo cáo', 'Quản trị', '5'),
    ('[27]', 'Kết quả thống kê', 'Quản trị', '5.1, 5.2'),
    ('–', 'Phiếu kết quả khám, đơn thuốc (bản in)', 'Mọi vai trò', '3.3, 4.3'),
]
T.bang_du_lieu('Danh sách màn hình của hệ thống', ['Số', 'Màn hình', 'Người dùng', 'Chức năng'],
               [list(h) for h in MAN], [900, 3900, 2400, 1872], giua={0, 3})


def anh(ten_tep, chu_thich, rong=15):
    T.hinh_anh(ANH + ten_tep, chu_thich, rong=rong)


u3('Giao diện phân hệ Bệnh nhân')
p('Màn hình đăng nhập [1] dùng chung cho cả bốn vai trò; bệnh nhân đăng nhập bằng số điện thoại, chưa có tài khoản thì đăng ký ở [2].')
anh('n01-dang-nhap.jpg', 'Màn hình [1] Đăng nhập')
anh('n02-dang-ky.jpg', 'Màn hình [2] Đăng ký tài khoản bệnh nhân')
p('Trang chủ bệnh nhân [3] hiển thị lịch hẹn sắp tới, nút đặt lịch, đổi hoặc hủy lịch và các thông báo từ phòng khám.')
anh('n03-bn-trang-chu.jpg', 'Màn hình [3] Trang chủ Bệnh nhân')
p('Bệnh nhân chọn bác sĩ và một ngày trong 7 ngày tới ở màn [15]; mỗi ngày ghi số khung giờ còn trống, ngày bác sĩ nghỉ bị làm mờ. Ở '
  'màn [16] chỉ hiện các khung giờ còn chọn được. Màn [17] tóm tắt lịch hẹn và cho nhập lý do khám.')
anh('n04-bn-chon-bac-si.jpg', 'Màn hình [15] Chọn bác sĩ, ngày khám')
anh('n05-bn-chon-gio.jpg', 'Màn hình [16] Chọn khung giờ trống')
anh('n06-bn-xac-nhan.jpg', 'Màn hình [17] Xác nhận lịch hẹn')
p('Màn [18] liệt kê các lịch hẹn sắp tới với nút đổi, hủy; khi hủy, bệnh nhân chọn lý do ở màn [19]. Sau mỗi lần khám, bệnh nhân xem '
  'được kết quả khám và đơn thuốc ở màn [20], kèm lời nhắc mua thuốc theo đơn tại nhà thuốc.')
anh('n07-bn-lich-hen.jpg', 'Màn hình [18] Lịch hẹn của tôi')
anh('n08-bn-huy-lich.jpg', 'Màn hình [19] Xác nhận hủy lịch hẹn')
anh('n50-bn-ket-qua.jpg', 'Màn hình [20] Kết quả khám, đơn thuốc')
u3('Giao diện phân hệ Nhân viên')
p('Trang chủ nhân viên [4] cho biết số lịch hẹn hôm nay theo trạng thái, danh sách bệnh nhân sắp đến giờ hẹn kèm nút tiếp nhận, và các '
  'lối tắt Tiếp nhận bệnh nhân, Đặt lịch hộ (gọi điện, đến quầy), Khám không hẹn trước.')
anh('n10-nv-trang-chu.jpg', 'Màn hình [4] Trang chủ Nhân viên')
p('Khi bệnh nhân gọi điện hoặc đến quầy để hẹn, nhân viên tìm bệnh nhân theo số điện thoại, họ tên hoặc mã ở màn [14]; bệnh nhân chưa '
  'có hồ sơ được thêm bằng họ tên và số điện thoại. Lịch sắp tới của bệnh nhân hiện ngay tại đây để đổi hoặc hủy. Các bước tiếp theo '
  'dùng chung màn [15]–[17] với bệnh nhân, đầu mỗi màn có dải ghi rõ đang đặt lịch hộ cho ai.')
anh('n11-nv-dat-lich.jpg', 'Màn hình [14] Đặt lịch hộ – chọn bệnh nhân')
anh('n12-nv-chon-bac-si.jpg', 'Màn hình [15] khi nhân viên đặt lịch hộ')
anh('n14-nv-xac-nhan.jpg', 'Màn hình [17] Xác nhận lịch hẹn đặt hộ')
anh('n15-nv-dat-xong.jpg', 'Thông báo đặt lịch hộ thành công')
p('Đến ngày hẹn, nhân viên chọn lịch hẹn trong ngày ở màn [21], kiểm tra, bổ sung hồ sơ rồi lập phiếu tiếp nhận; người không hẹn '
  'trước được tạo hồ sơ và chọn bác sĩ ngay trên màn này. Danh sách lịch hẹn [22] lọc được theo bác sĩ, chia nhóm theo ngày, nút thao '
  'tác thay đổi theo trạng thái: Tiếp nhận, Đổi, Hủy hoặc In phiếu.')
anh('n16-nv-tiep-nhan.jpg', 'Màn hình [21] Tiếp nhận bệnh nhân')
anh('n17-nv-lich-hen.jpg', 'Màn hình [22] Danh sách lịch hẹn')
u3('Giao diện phân hệ Bác sĩ')
p('Bác sĩ đăng nhập vào thẳng hàng chờ khám của mình [5]: các bệnh nhân đã được nhân viên tiếp nhận hiện theo giờ hẹn, bác sĩ bấm Gọi '
  'vào khám để chuyển sang Đang khám. Màn [23] cho xem các lần khám trước của bệnh nhân.')
anh('n20-bs-hang-cho.jpg', 'Màn hình [5] Hàng chờ khám của bác sĩ')
anh('n21-bs-lich-su.jpg', 'Màn hình [23] Hồ sơ bệnh án các lần trước')
p('Màn [24] gồm phần kết quả khám, chẩn đoán và phần kê đơn thuốc. Thuốc được chọn từ danh mục; thuốc trùng trong đơn bị báo lỗi ngay. '
  'Bệnh án và đơn thuốc được lưu cùng lúc; phiếu kết quả khám và đơn thuốc được in để bệnh nhân mang ra nhà thuốc mua thuốc. Bác sĩ '
  'xem các ca làm việc và lịch hẹn 7 ngày tới ở màn [25].')
anh('n22-bs-ghi-ket-qua.jpg', 'Màn hình [24] Ghi kết quả khám')
anh('n23-bs-ke-don.jpg', 'Kê đơn thuốc từ danh mục thuốc')
anh('n24-bs-luu-xong.jpg', 'Bệnh án và đơn thuốc đã được lưu')
anh('n26-phieu-in.jpg', 'Phiếu kết quả khám và đơn thuốc khi in', rong=11)
anh('n25-bs-lich-lam-viec.jpg', 'Màn hình [25] Lịch làm việc của tôi')
u3('Giao diện phân hệ Quản trị')
p('Trang chủ quản trị [6] cho biết tình hình trong ngày của cả phòng khám: số lịch hẹn, số đã khám, số đang chờ, số đơn thuốc đã kê, '
  'lịch hẹn theo từng bác sĩ, các thuốc được kê nhiều nhất 30 ngày qua và số tài khoản đang hoạt động.')
anh('n40-qt-trang-chu.jpg', 'Màn hình [6] Trang chủ Quản trị')
p('Nhóm màn hình quản trị hệ thống gồm danh sách bác sĩ [7], biểu mẫu bác sĩ [8], lịch làm việc [9], danh mục thuốc [10] và biểu mẫu '
  'thuốc [11]. Ở màn [12], quản trị xem tài khoản theo vai trò, khóa hoặc mở khóa; màn [13] tạo, sửa tài khoản, tài khoản bác sĩ phải '
  'gắn với một bác sĩ chưa có tài khoản.')
anh('n46-qt-bac-si.jpg', 'Màn hình [7] Danh sách bác sĩ')
anh('n47-qt-bac-si-form.jpg', 'Màn hình [8] Sửa thông tin bác sĩ')
anh('n48-qt-lich-lam-viec.jpg', 'Màn hình [9] Lịch làm việc bác sĩ')
anh('n35-qt-thuoc.jpg', 'Màn hình [10] Danh mục thuốc')
anh('n36-qt-thuoc-form.jpg', 'Màn hình [11] Thêm thuốc mới')
anh('n41-qt-tai-khoan.jpg', 'Màn hình [12] Tài khoản người dùng')
anh('n42-qt-tai-khoan-form.jpg', 'Màn hình [13] Thêm tài khoản')
p('Màn [26] chọn loại báo cáo và khoảng thời gian; màn [27] hiển thị thống kê lượt khám hoặc thống kê thuốc đã kê và in được báo cáo.')
anh('n43-qt-bao-cao.jpg', 'Màn hình [26] Chọn loại báo cáo')
anh('n45-qt-bao-cao-luot-kham.jpg', 'Màn hình [27] Báo cáo lượt khám')
anh('n49-qt-bao-cao-thuoc.jpg', 'Màn hình [27] Báo cáo thuốc đã kê')

# =====================================================================================
# PHỤ LỤC
# =====================================================================================
T.tieu_de_rieng('Phụ lục')
T.tieu_de_phu_luc('Phụ lục A. Kế hoạch khảo sát')
T.bang_du_lieu(None, ['Đối tượng', 'Phương pháp', 'Nội dung tìm hiểu', 'Thời gian'], [
    ['Quản trị phòng khám', 'Phỏng vấn trực tiếp theo bộ câu hỏi (Phụ lục B)', 'Tổ chức phòng khám, nhân sự, quy trình chung, danh mục thuốc, các báo cáo cần có; khó khăn hiện tại', '…/…/2026'],
    ['Nhân viên quầy tiếp đón, bác sĩ', 'Phỏng vấn theo nhóm câu hỏi của bộ phận mình (Phụ lục B)', 'Hẹn lịch qua điện thoại, tiếp nhận; khám bệnh, kê đơn', '…/…/2026'],
    ['Quầy tiếp đón, phòng khám trong giờ khám', 'Quan sát, ghi chép', 'Cách ghi sổ hẹn, gọi bệnh nhân, lưu phiếu khám và đơn thuốc; thời gian chờ', '…/…/2026'],
    ['Bệnh nhân đến khám', 'Phiếu khảo sát (Phụ lục C)', 'Cách đặt lịch hiện nay, thời gian chờ, mong muốn với đặt lịch trực tuyến', '…/…/2026'],
    ['Giấy tờ nghiệp vụ', 'Thu thập mẫu', 'Sổ hẹn lịch, phiếu kết quả khám, đơn thuốc, bảng tổng hợp cuối tháng', '…/…/2026'],
], [2200, 2300, 3172, 1400], giua={3})
T.tieu_de_phu_luc('Phụ lục B. Câu hỏi phỏng vấn nhân viên phòng khám')
p('Người được phỏng vấn: ……………………………   Bộ phận: ……………………   Ngày phỏng vấn: …/…/2026')
CAU_HOI = [
    ('Về tổ chức phòng khám (quản trị)', [
        'Phòng khám có bao nhiêu bác sĩ, thuộc những chuyên khoa nào? Mỗi bác sĩ khám ở phòng nào?',
        'Phòng khám có những bộ phận nào (quầy tiếp đón, phòng khám…), mỗi bộ phận mấy người, làm những việc gì?',
        'Lịch làm việc của bác sĩ được sắp xếp thế nào (ca sáng, ca chiều, ngày nghỉ)? Bao lâu thì thay đổi?',
        'Mỗi ngày phòng khám tiếp nhận khoảng bao nhiêu lượt khám? Thời gian khám trung bình cho một bệnh nhân là bao lâu?']),
    ('Về đặt lịch và tiếp nhận (nhân viên quầy tiếp đón)', [
        'Hiện nay bệnh nhân hẹn lịch bằng cách nào (gọi điện, đến trực tiếp, nhắn tin)? Ai là người ghi lịch hẹn?',
        'Khi bệnh nhân gọi điện để hẹn, nhân viên cần hỏi những thông tin gì? Có ghi lại ai là người nhận lịch không?',
        'Đã từng xảy ra trường hợp hai bệnh nhân bị hẹn trùng giờ với một bác sĩ chưa? Khi đó xử lý ra sao?',
        'Bệnh nhân muốn đổi hoặc hủy lịch thì báo cho phòng khám thế nào? Có cần ghi lại lý do hủy không?',
        'Khi bệnh nhân đến, nhân viên làm những việc gì để tiếp nhận? Cần kiểm tra những thông tin nào?',
        'Phòng khám có nhận người đến khám không hẹn trước không? Họ được xếp khám thế nào?']),
    ('Về khám bệnh và kê đơn (bác sĩ)', [
        'Bác sĩ biết bệnh nhân nào đang chờ mình bằng cách nào? Ai gọi bệnh nhân vào khám?',
        'Kết quả khám gồm những thông tin gì (chỉ số sinh tồn, kết quả, chẩn đoán, lời dặn)? Ai ghi lại?',
        'Khi bệnh nhân tái khám, bác sĩ xem lại các lần khám trước bằng cách nào?',
        'Khi kê đơn có những lỗi nào hay gặp (sai tên thuốc, ghi trùng thuốc, sai số lượng)? Phòng khám có danh mục thuốc thường kê không?',
        'Bệnh nhân nhận phiếu kết quả khám và đơn thuốc dưới dạng nào? Bệnh nhân mua thuốc ở đâu? Phòng khám lưu bản sao thế nào?']),
    ('Về báo cáo và mong muốn (quản trị)', [
        'Cuối tháng phòng khám cần tổng hợp những số liệu gì (lượt khám, thuốc đã kê)? Hiện làm bằng cách nào, mất bao lâu?',
        'Anh/chị mong muốn phần mềm hỗ trợ những việc gì nhất? Việc nào nên để bệnh nhân tự làm?',
        'Mỗi vai trò nên được xem và sửa những thông tin nào? Khi nhân viên nghỉ việc thì tài khoản xử lý thế nào?',
        'Thông tin nào của bệnh nhân cần được giữ kín, chỉ một số người được xem?']),
]
for nhom, ds in CAU_HOI:
    T.nhom(nhom)
    for c in ds:
        gach(c, 22)
T.tieu_de_phu_luc('Phụ lục C. Phiếu khảo sát bệnh nhân')
T.giua('PHIẾU KHẢO SÁT Ý KIẾN BỆNH NHÂN VỀ VIỆC ĐẶT LỊCH KHÁM', dam=True)
p('Nhằm xây dựng hệ thống đặt lịch và quản lý khám bệnh thuận tiện hơn, mong Anh/Chị dành ít phút trả lời các câu hỏi dưới đây bằng '
  'cách đánh dấu vào ô phù hợp. Thông tin chỉ dùng cho mục đích học tập.')
p('Họ tên (không bắt buộc): …………………………   Năm sinh: ……………   Ngày: …/…/2026')
T.bang_du_lieu(None, ['STT', 'Câu hỏi', 'Lựa chọn'], [
    ['1', 'Anh/Chị đến phòng khám bao nhiêu lần trong 6 tháng gần đây?', '□ 1 lần    □ 2–3 lần    □ Trên 3 lần'],
    ['2', 'Anh/Chị thường hẹn lịch khám bằng cách nào?', '□ Gọi điện    □ Đến trực tiếp    □ Nhắn tin    □ Không hẹn, đến là khám'],
    ['3', 'Thời gian chờ từ lúc đến tới lúc được khám thường là bao lâu?', '□ Dưới 15 phút    □ 15–30 phút    □ 30–60 phút    □ Trên 60 phút'],
    ['4', 'Anh/Chị đã từng gặp tình trạng đến đúng giờ hẹn nhưng phải chờ vì trùng lịch chưa?', '□ Chưa    □ Thỉnh thoảng    □ Thường xuyên'],
    ['5', 'Anh/Chị đã từng gặp khó khăn khi đọc đơn thuốc viết tay hoặc khi mua thuốc theo đơn ở nhà thuốc chưa?', '□ Chưa    □ Thỉnh thoảng    □ Thường xuyên'],
    ['6', 'Anh/Chị có sử dụng điện thoại thông minh hoặc máy tính có mạng không?', '□ Có    □ Không'],
    ['7', 'Anh/Chị muốn đặt lịch khám theo cách nào?', '□ Tự đặt trên web    □ Gọi điện nhờ nhân viên đặt    □ Đến quầy đặt'],
    ['8', 'Khi đặt lịch, Anh/Chị muốn chọn những gì? (có thể chọn nhiều)', '□ Bác sĩ    □ Ngày khám    □ Giờ khám    □ Ghi lý do khám'],
    ['9', 'Anh/Chị có muốn xem lại kết quả khám và đơn thuốc trên web không?', '□ Có    □ Không'],
    ['10', 'Anh/Chị muốn nhận thông báo về lịch hẹn qua đâu?', '□ Trên trang web    □ Tin nhắn    □ Email    □ Không cần'],
    ['11', 'Góp ý khác để việc khám bệnh thuận tiện hơn:', '……………………………………………………………………'],
], [700, 4400, 3972], giua={0})
T.giua('Xin chân thành cảm ơn Anh/Chị!')
T.tieu_de_phu_luc('Phụ lục D. Bảng tổng hợp kết quả khảo sát')
p('Bảng dùng để tổng hợp số phiếu khảo sát bệnh nhân thu về (điền sau khi khảo sát).')
T.bang_du_lieu(None, ['Câu', 'Lựa chọn', 'Số phiếu', 'Tỉ lệ (%)'], [
    ['2', 'Gọi điện', '', ''], ['', 'Đến trực tiếp', '', ''], ['', 'Nhắn tin', '', ''], ['', 'Không hẹn, đến là khám', '', ''],
    ['4', 'Đã gặp trùng lịch (thỉnh thoảng hoặc thường xuyên)', '', ''],
    ['5', 'Đã gặp khó khăn khi đọc hoặc mua thuốc theo đơn', '', ''],
    ['7', 'Muốn tự đặt lịch trên web', '', ''], ['', 'Muốn gọi điện nhờ nhân viên đặt', '', ''], ['', 'Muốn đến quầy đặt', '', ''],
    ['9', 'Muốn xem kết quả khám, đơn thuốc trên web', '', ''],
    ['', 'Tổng số phiếu thu về', '', ''],
], [900, 4772, 1700, 1700], giua={0, 2, 3})
